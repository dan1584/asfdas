<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

class CPanelBrowser {
    private $baseUrl;
    private $auth;
    private $currentDir;
    private $username;
    
    public function __construct($domain, $username, $apiToken, $currentDir = '') {
        $this->baseUrl = "https://{$domain}:2083";
        $this->auth = "cpanel {$username}:{$apiToken}";
        $this->username = $username;
        $this->currentDir = $currentDir ?: "/home/{$username}/public_html";
    }

    public function testConnection() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Fileman/list_files?dir=' . urlencode($this->currentDir),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }

    public function listDir() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Fileman/list_files?dir=' . urlencode($this->currentDir),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function uploadFile($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Invalid file upload');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Fileman/upload_files',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'dir' => $this->currentDir,
                'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name'])
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function viewFile($filename) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Fileman/get_file_content?dir=' . urlencode($this->currentDir) . '&file=' . urlencode($filename),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        
        if (isset($result['data']['content'])) {
            return $result['data']['content'];
        } elseif (isset($result['data'])) {
            return $result['data'];
        }
        return 'Unable to read file content';
    }

    public function createFile($filename, $content = '', $fallback = 1, $fromCharset = 'UTF-8', $toCharset = 'UTF-8') {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Fileman/save_file_content',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'dir' => $this->currentDir,
                'file' => $filename,
                'content' => $content,
                'fallback' => $fallback,
                'from_charset' => $fromCharset,
                'to_charset' => $toCharset
            ])
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function createFolder($foldername) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/json-api/cpanel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'cpanel_jsonapi_module' => 'Fileman',
                'cpanel_jsonapi_func' => 'mkdir',
                'path' => $this->currentDir,
                'name' => $foldername
            ])
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        if (!isset($result['cpanelresult']['event']['result']) || !$result['cpanelresult']['event']['result']) {
            throw new Exception('API error: ' . (isset($result['cpanelresult']['errors'][0]) ? $result['cpanelresult']['errors'][0] : 'Unknown error'));
        }
        return $result;
    }

    public function renameItem($oldName, $newName, $isDir = false) {
        $sourcePath = $this->currentDir . '/' . $oldName;
        $destPath = $this->currentDir . '/' . $newName;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/json-api/cpanel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'cpanel_jsonapi_module' => 'Fileman',
                'cpanel_jsonapi_func' => 'fileop',
                'cpanel_jsonapi_apiversion' => 2,
                'op' => 'rename',
                'sourcefiles' => $sourcePath,
                'destfiles' => $destPath
            ])
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        $eventResult = isset($result['cpanelresult']['event']['result']) && $result['cpanelresult']['event']['result'];
        $dataResult = (isset($result['cpanelresult']['data'][0]['result']) && $result['cpanelresult']['data'][0]['result']) ||
                      (isset($result['cpanelresult']['data']['result']) && $result['cpanelresult']['data']['result']);

        if (!$eventResult || !$dataResult) {
            $errorMessage = isset($result['cpanelresult']['error']) ? $result['cpanelresult']['error'] :
                           (isset($result['cpanelresult']['data']['reason']) ? $result['cpanelresult']['data']['reason'] : 'Unknown error');
            throw new Exception('API error: ' . $errorMessage);
        }

        return $result;
    }

    public function deleteFile($filename) {
        $sourcefiles = $this->currentDir . '/' . $filename;
        if (empty($this->currentDir) || strpos($sourcefiles, '/home/') !== 0) {
            throw new Exception('Invalid path: ' . $sourcefiles);
        }

        // Check if the file exists
        $contents = $this->listDir();
        $fileExists = false;
        if (!empty($contents['data'])) {
            foreach ($contents['data'] as $item) {
                if ($item['file'] === $filename && $item['type'] !== 'dir') {
                    $fileExists = true;
                    break;
                }
            }
        }
        if (!$fileExists) {
            throw new Exception('File does not exist: ' . $sourcefiles);
        }

        // Use cPanel API to delete the file
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/json-api/cpanel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'cpanel_jsonapi_module' => 'Fileman',
                'cpanel_jsonapi_func' => 'fileop',
                'cpanel_jsonapi_apiversion' => 2,
                'filelist' => 1,
                'multiform' => 1,
                'doubledecode' => 0,
                'op' => 'trash',
                'metadata' => '[object Object]',
                'sourcefiles' => $sourcefiles
            ])
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        $eventResult = isset($result['cpanelresult']['event']['result']) && $result['cpanelresult']['event']['result'];
        $dataResult = (isset($result['cpanelresult']['data'][0]['result']) && $result['cpanelresult']['data'][0]['result']) ||
                      (isset($result['cpanelresult']['data']['result']) && $result['cpanelresult']['data']['result']);

        if (!$eventResult || !$dataResult) {
            $errorMessage = isset($result['cpanelresult']['error']) ? $result['cpanelresult']['error'] :
                            (isset($result['cpanelresult']['data']['reason']) ? $result['cpanelresult']['data']['reason'] : 'Unknown error');
            throw new Exception('API error: ' . $errorMessage);
        }

        return $result;
    }

    public function deleteFolder($foldername) {
        $sourcefiles = $this->currentDir . '/' . $foldername;
        if (empty($this->currentDir) || strpos($sourcefiles, '/home/') !== 0) {
            throw new Exception('Invalid path: ' . $sourcefiles);
        }

        $contents = $this->listDir();
        $folderExists = false;
        if (!empty($contents['data'])) {
            foreach ($contents['data'] as $item) {
                if ($item['file'] === $foldername && $item['type'] === 'dir') {
                    $folderExists = true;
                    break;
                }
            }
        }
        if (!$folderExists) {
            throw new Exception('Folder does not exist: ' . $sourcefiles);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/json-api/cpanel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'cpanel_jsonapi_module' => 'Fileman',
                'cpanel_jsonapi_func' => 'fileop',
                'cpanel_jsonapi_apiversion' => 2,
                'filelist' => 1,
                'multiform' => 1,
                'doubledecode' => 0,
                'op' => 'trash',
                'metadata' => '[object Object]',
                'sourcefiles' => $sourcefiles
            ])
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        $eventResult = isset($result['cpanelresult']['event']['result']) && $result['cpanelresult']['event']['result'];
        $dataResult = (isset($result['cpanelresult']['data'][0]['result']) && $result['cpanelresult']['data'][0]['result']) ||
                      (isset($result['cpanelresult']['data']['result']) && $result['cpanelresult']['data']['result']);

        if (!$eventResult || !$dataResult) {
            $errorMessage = isset($result['cpanelresult']['error']) ? $result['cpanelresult']['error'] :
                            (isset($result['cpanelresult']['data']['reason']) ? $result['cpanelresult']['data']['reason'] : 'Unknown error');
            throw new Exception('API error: ' . $errorMessage);
        }

        return $result;
    }

    public function listFTPAccounts($skipAcctTypes = 'main|logaccess') {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Ftp/list_ftp?skip_acct_types=' . urlencode($skipAcctTypes),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function createFTPAccount($username, $password, $quota = 0, $directory = '', $domain = '', $disallowdot = 1) {
        if (empty($directory)) {
            $directory = $this->currentDir;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception('Password must be at least 8 characters long and contain both letters and numbers');
        }

        $params = [
            'user' => $username,
            'pass' => $password,
            'quota' => $quota,
            'homedir' => $directory,
            'disallowdot' => $disallowdot
        ];

        if (!empty($domain)) {
            $params['domain'] = $domain;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Ftp/add_ftp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (isset($result['status']) && !$result['status']) {
            throw new Exception('API error: ' . (isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error'));
        }
        return $result;
    }

    public function deleteFTPAccount($user, $domain = '', $destroy = 0) {
        $userParts = explode('@', $user);
        $username = $userParts[0];
        $params = [
            'user' => $username,
            'destroy' => $destroy
        ];

        if (count($userParts) === 1 && !empty($domain)) {
            $params['domain'] = $domain;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/Ftp/delete_ftp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function listDNSRecords($zone) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/DNS/parse_zone?zone=' . urlencode($zone),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        file_put_contents('dns_response.log', print_r($result, true));
        
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }

        $serial = null;
        if (!empty($result['data'])) {
            foreach ($result['data'] as $record) {
                $recordType = isset($record['record_type']) ? $record['record_type'] : (isset($record['type']) ? $record['type'] : null);
                if ($recordType === 'SOA') {
                    if (isset($record['data']) && is_array($record['data']) && count($record['data']) >= 3) {
                        $serial = $record['data'][2];
                    } elseif (isset($record['data_b64']) && is_array($record['data_b64']) && count($record['data_b64']) >= 3) {
                        $decodedData = array_map('base64_decode', $record['data_b64']);
                        $serial = $decodedData[2];
                    }
                    break;
                }
            }
        }

        if ($serial === null) {
            file_put_contents('dns_response.log', "Warning: Could not find SOA record or extract Serial Number for zone $zone\n", FILE_APPEND);
            $serial = $this->fetchSerialNumberFallback($zone);
        }

        return [
            'records' => $result,
            'serial' => $serial
        ];
    }

    private function fetchSerialNumberFallback($zone) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/DNS/lookup?type=SOA&domain=' . urlencode($zone),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            file_put_contents('dns_response.log', "Fallback cURL error: " . curl_error($ch) . "\n", FILE_APPEND);
            curl_close($ch);
            return time();
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        file_put_contents('dns_response.log', "Fallback DNS::lookup response: " . print_r($result, true) . "\n", FILE_APPEND);
        
        if (!empty($result['data'])) {
            foreach ($result['data'] as $record) {
                if (isset($record['type']) && $record['type'] === 'SOA') {
                    if (isset($record['answer']) && is_array($record['answer']) && count($record['answer']) >= 3) {
                        return $record['answer'][2];
                    }
                }
            }
        }
        
        return time();
    }

    public function editDNSRecord($zone, $serial, $editData) {
        $params = [
            'zone' => $zone,
            'serial' => $serial,
            'edit' => json_encode($editData)
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/DNS/mass_edit_zone',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function addDNSRecord($domain, $name, $type, $address, $ttl = 14400) {
        // Normalize the DNS name
        if ($name === "@" || empty($name)) {
            $name = $domain . '.';
        } elseif (strpos($name, '.') === false) {
            $name = $name . '.' . $domain . '.';
        }
    
        // Check for existing records with the same name and type, and delete if found
        $existingRecords = $this->listDNSRecords($domain);
        if (!empty($existingRecords['records']['data'])) {
            foreach ($existingRecords['records']['data'] as $record) {
                $recordName = isset($record['dname_b64']) ? base64_decode($record['dname_b64']) : (isset($record['name']) ? $record['name'] : '');
                $recordType = isset($record['record_type']) ? $record['record_type'] : (isset($record['type']) ? $record['type'] : '');
                if ($recordName === $name && $recordType === $type) {
                    $lineIndex = isset($record['line_index']) ? $record['line_index'] : (isset($record['line']) ? $record['line'] : null);
                    if ($lineIndex !== null) {
                        $this->deleteDNSRecord($domain, $lineIndex);
                    }
                    break;
                }
            }
        }
    
        // Add the new DNS record using ZoneEdit::add_zone_record
        $params = [
            'cpanel_jsonapi_user' => $this->username,
            'cpanel_jsonapi_apiversion' => '2',
            'cpanel_jsonapi_module' => 'ZoneEdit',
            'cpanel_jsonapi_func' => 'add_zone_record',
            'domain' => $domain,
            'name' => $name,
            'type' => $type,
            'address' => $address,
            'ttl' => $ttl
        ];
    
        $url = $this->baseUrl . '/json-api/cpanel?' . http_build_query($params);
    
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
    
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
    
        $result = json_decode($response, true);
        if (isset($result['cpanelresult']['error'])) {
            throw new Exception('API error: ' . $result['cpanelresult']['error']);
        }
        if (!isset($result['cpanelresult']['event']['result']) || !$result['cpanelresult']['event']['result']) {
            $error = isset($result['cpanelresult']['errors'][0]) ? $result['cpanelresult']['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
    
        return $result;
    }
    
    public function deleteDNSRecord($domain, $line) {
        $params = [
            'cpanel_jsonapi_user' => $this->username,
            'cpanel_jsonapi_apiversion' => '2',
            'cpanel_jsonapi_module' => 'ZoneEdit',
            'cpanel_jsonapi_func' => 'remove_zone_record',
            'domain' => $domain,
            'line' => $line
        ];
    
        $url = $this->baseUrl . '/json-api/cpanel?' . http_build_query($params);
    
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
    
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
    
        $result = json_decode($response, true);
        if (isset($result['cpanelresult']['error'])) {
            throw new Exception('API error: ' . $result['cpanelresult']['error']);
        }
        if (!isset($result['cpanelresult']['event']['result']) || !$result['cpanelresult']['event']['result']) {
            $error = isset($result['cpanelresult']['errors'][0]) ? $result['cpanelresult']['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
    
        return $result;
    }

    public function addSubdomain($subdomain, $rootdomain, $options = []) {
        $params = [
            'domain' => $subdomain,
            'rootdomain' => $rootdomain,
            'canoff' => $options['canoff'] ?? 1,
            'dir' => $options['dir'] ?? '',
            'disallowdot' => $options['disallowdot'] ?? 0
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/SubDomain/addsubdomain',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function listModSecurityDomains() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/ModSecurity/list_domains',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth]
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function enableModSecurityDomains($domains) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/ModSecurity/enable_domains',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'domains' => $domains
            ])
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function disableModSecurityDomains($domains) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/ModSecurity/disable_domains',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'domains' => $domains
            ])
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }

    public function disableAllModSecurityDomains() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/execute/ModSecurity/disable_all_domains',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->auth],
            CURLOPT_POST => true
        ]);
        
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['status']) || !$result['status']) {
            $error = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            throw new Exception('API error: ' . $error);
        }
        return $result;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (empty($_POST['domain']) || empty($_POST['username']) || empty($_POST['apiToken'])) {
        $loginError = 'All fields are required';
    } else {
        try {
            $browser = new CPanelBrowser(
                $_POST['domain'],
                $_POST['username'],
                $_POST['apiToken']
            );
            
            if ($browser->testConnection()) {
                $_SESSION['cpanel'] = [
                    'domain' => $_POST['domain'],
                    'username' => $_POST['username'],
                    'apiToken' => $_POST['apiToken']
                ];
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $loginError = 'Invalid credentials or connection failed';
            }
        } catch (Exception $e) {
            $loginError = 'Connection error: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['cpanel']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['cpanel']) || 
    !isset($_SESSION['cpanel']['domain']) || 
    !isset($_SESSION['cpanel']['username']) || 
    !isset($_SESSION['cpanel']['apiToken'])) {
    unset($_SESSION['cpanel']);
    ?>
    <!DOCTYPE html>
    <html class="dark">
    <head>
        <meta charset="UTF-8">
        <title>cPanel Manager</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body {
                background-color: #1f2937;
                color: #e5e7eb;
            }
            .animate-slide-up {
                animation: slideUp 0.3s ease-out;
            }
            @keyframes slideUp {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-gray-800 rounded-xl shadow-2xl animate-slide-up">
            <h1 class="text-3xl font-bold mb-8 text-center text-white">cPanel Login</h1>
            
            <?php if (isset($loginError)): ?>
                <div class="mb-6 p-4 bg-red-900/50 text-red-200 rounded-lg">
                    <?php echo htmlspecialchars($loginError); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Domain</label>
                    <input type="text" name="domain" 
                           class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400 transition-all" 
                           placeholder="example.com" required>
                </div>

                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Username</label>
                    <input type="text" name="username" 
                           class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400 transition-all" 
                           required>
                </div>

                <div>
                    <label class="block text-gray-300 mb-2 font-medium">API Token</label>
                    <input type="password" name="apiToken" 
                           class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400 transition-all" 
                           required>
                </div>

                <button type="submit" name="login" 
                        class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/50 transition-all font-medium">
                    Login
                </button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$currentDir = $_GET['dir'] ?? $_POST['dir'] ?? '/home/' . $_SESSION['cpanel']['username'] . '/public_html';

$browser = new CPanelBrowser(
    $_SESSION['cpanel']['domain'],
    $_SESSION['cpanel']['username'],
    $_SESSION['cpanel']['apiToken'],
    $currentDir
);

if (isset($_GET['view'])) {
    $fileContent = $browser->viewFile($_GET['view']);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $fileContent;
    exit;
}

$ftpMessage = '';
$ftpAccounts = [];
$modsecMessage = '';
$modsecDomains = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_ftp' && !empty($_POST['login']) && !empty($_POST['password'])) {
            try {
                $result = $browser->createFTPAccount(
                    $_POST['login'],
                    $_POST['password'],
                    $_POST['quota'] === 'unlimited' ? 0 : (int)$_POST['quota'],
                    $_POST['homedir'] ?? $currentDir,
                    $_SESSION['cpanel']['domain'],
                    $_POST['disallowdot'] ?? 1
                );
                $ftpMessage = $result['status'] ? 'FTP account created successfully' : 'Failed to create FTP account: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $ftpMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete_ftp' && !empty($_POST['user'])) {
            try {
                $result = $browser->deleteFTPAccount(
                    $_POST['user'],
                    $_SESSION['cpanel']['domain'],
                    $_POST['destroy'] ?? 0
                );
                $ftpMessage = $result['status'] ? 'FTP account deleted successfully' : 'Failed to delete FTP account: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $ftpMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'enable_modsec' && !empty($_POST['domain'])) {
            try {
                $result = $browser->enableModSecurityDomains($_POST['domain']);
                $modsecMessage = $result['status'] ? 'ModSecurity enabled successfully' : 'Failed to enable ModSecurity: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $modsecMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'disable_modsec' && !empty($_POST['domain'])) {
            try {
                $result = $browser->disableModSecurityDomains($_POST['domain']);
                $modsecMessage = $result['status'] ? 'ModSecurity disabled successfully' : 'Failed to disable ModSecurity: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $modsecMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'disable_all_modsec') {
            try {
                $result = $browser->disableAllModSecurityDomains();
                $modsecMessage = $result['status'] ? 'ModSecurity disabled for all domains successfully' : 'Failed to disable ModSecurity for all domains: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $modsecMessage = 'Error: ' . $e->getMessage();
            }
        }
    }
}

try {
    $ftpAccounts = $browser->listFTPAccounts();
} catch (Exception $e) {
    $ftpMessage = 'Error fetching FTP accounts: ' . $e->getMessage();
}

try {
    $modsecDomains = $browser->listModSecurityDomains();
} catch (Exception $e) {
    $modsecMessage = 'Error fetching ModSecurity domains: ' . $e->getMessage();
}

$dnsMessage = '';
$dnsRecords = [];
$serialNumber = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_subdomain' && !empty($_POST['subdomain'])) {
            try {
                $options = [
                    'canoff' => $_POST['canoff'] ?? 1,
                    'dir' => $_POST['dir'] ?? '',
                    'disallowdot' => $_POST['disallowdot'] ?? 0
                ];
                $result = $browser->addSubdomain(
                    $_POST['subdomain'],
                    $_SESSION['cpanel']['domain'],
                    $options
                );
                $dnsMessage = $result['status'] ? 'Subdomain created successfully' : 'Failed to create subdomain: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $dnsMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'edit_dns' && !empty($_POST['line_index']) && !empty($_POST['serial'])) {
            try {
                $editData = [
                    'line_index' => (int)$_POST['line_index'],
                    'dname' => $_POST['dname'],
                    'ttl' => (int)$_POST['ttl'],
                    'record_type' => $_POST['record_type'],
                    'data' => explode(',', $_POST['data'])
                ];
                $result = $browser->editDNSRecord(
                    $_SESSION['cpanel']['domain'],
                    $_POST['serial'],
                    $editData
                );
                $dnsMessage = $result['status'] ? 'DNS record updated successfully' : 'Failed to update DNS record: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $dnsMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'add_dns') {
            try {
                $result = $browser->addDNSRecord(
                    $_SESSION['cpanel']['domain'],
                    $_POST['dname'],
                    $_POST['record_type'],
                    $_POST['address'],
                    (int)$_POST['ttl']
                );
                $dnsMessage = 'DNS record added successfully!';
            } catch (Exception $e) {
                $dnsMessage = 'Error: ' . $e->getMessage();
            }
        }
    }
}

$dnsRecordsData = [];
try {
    $dnsRecordsData = $browser->listDNSRecords($_SESSION['cpanel']['domain']);
    $dnsRecords = $dnsRecordsData['records'];
    $serialNumber = $dnsRecordsData['serial'];
} catch (Exception $e) {
    $dnsMessage = 'Error fetching DNS records: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        try {
            $result = $browser->uploadFile($_FILES['file']);
            $uploadMessage = $result['status'] ? 'File uploaded successfully' : 'Upload failed: ' . ($result['errors'][0] ?? 'Unknown error');
        } catch (Exception $e) {
            $uploadMessage = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_file' && !empty($_POST['filename'])) {
            try {
                $result = $browser->createFile(
                    $_POST['filename'],
                    $_POST['content'] ?? '',
                    $_POST['fallback'] ?? 1,
                    $_POST['from_charset'] ?? 'UTF-8',
                    $_POST['to_charset'] ?? 'UTF-8'
                );
                $createMessage = $result['status'] ? 'File created successfully' : 'Failed to create file: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $createMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'edit_file' && !empty($_POST['filename']) && isset($_POST['content'])) {
            try {
                $result = $browser->createFile(
                    $_POST['filename'],
                    $_POST['content'],
                    $_POST['fallback'] ?? 1,
                    $_POST['from_charset'] ?? 'UTF-8',
                    $_POST['to_charset'] ?? 'UTF-8'
                );
                $editMessage = $result['status'] ? 'File updated successfully' : 'Failed to update file: ' . ($result['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $editMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'create_folder' && !empty($_POST['foldername'])) {
            try {
                $result = $browser->createFolder($_POST['foldername']);
                $createMessage = $result['cpanelresult']['event']['result'] ? 'Folder created successfully' : 'Failed to create folder: ' . ($result['cpanelresult']['errors'][0] ?? 'Unknown error');
            } catch (Exception $e) {
                $createMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete_file' && !empty($_POST['filename'])) {
            try {
                $result = $browser->deleteFile($_POST['filename']);
                $deleteMessage = 'File deleted successfully';
            } catch (Exception $e) {
                $deleteMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete_folder' && !empty($_POST['foldername'])) {
            try {
                $result = $browser->deleteFolder($_POST['foldername']);
                $deleteMessage = 'Folder deleted successfully';
            } catch (Exception $e) {
                $deleteMessage = 'Error: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'rename' && !empty($_POST['old_name']) && !empty($_POST['new_name'])) {
            try {
                $isDir = $_POST['type'] === 'dir';
                $result = $browser->renameItem($_POST['old_name'], $_POST['new_name'], $isDir);
                $renameMessage = 'Renamed successfully';
            } catch (Exception $e) {
                $renameMessage = 'Error: ' . $e->getMessage();
            }
        }
    }
}

$contents = $browser->listDir();

$pathParts = explode('/', trim($currentDir, '/'));
$breadcrumb = [];
$accumulatedPath = '';
foreach ($pathParts as $part) {
    $accumulatedPath .= '/' . $part;
    $breadcrumb[] = ['name' => $part, 'path' => $accumulatedPath];
}

$section = $_GET['section'] ?? 'files';
?>

<!DOCTYPE html>
<html class="dark">
<head>
    <meta charset="UTF-8">
    <title>cPanel Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #1f2937;
            color: #e5e7eb;
        }
        .file-item:hover {
            background-color: #374151;
            transition: all 0.2s ease;
        }
        textarea {
            background-color: #2d3748;
            color: #e5e7eb;
            border-color: #4b5563;
        }
        input, button {
            transition: all 0.2s ease;
        }
        .modal {
            transition: all 0.3s ease;
            transform: scale(0.95);
            opacity: 0;
        }
        .modal.show {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <div class="w-64 bg-gray-900 p-6 flex flex-col justify-between h-screen">
        <div>
            <h1 class="text-xl font-bold text-blue-400 mb-8">cPanel Manager</h1>
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-400 mb-3">DOMAIN</h3>
                <p class="text-gray-200"><?php echo htmlspecialchars($_SESSION['cpanel']['domain']); ?></p>
            </div>
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-400 mb-3">NAVIGATION</h3>
                <a href="?section=files" class="flex items-center text-gray-200 hover:text-blue-400 mb-2">
                    <i class="fas fa-folder mr-2"></i>
                    File Manager
                </a>
                <a href="?section=ftp" class="flex items-center text-gray-200 hover:text-blue-400 mb-2">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    FTP Manager
                </a>
                <a href="?section=dns" class="flex items-center text-gray-200 hover:text-blue-400 mb-2">
                    <i class="fas fa-globe mr-2"></i>
                    DNS/Subdomain Manager
                </a>
                <a href="?section=modsec" class="flex items-center text-gray-200 hover:text-blue-400">
                    <i class="fas fa-shield-alt mr-2"></i>
                    ModSecurity Manager
                </a>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-400 mb-3">SERVER INFO</h3>
            <p class="text-gray-200">Username: <?php echo htmlspecialchars($_SESSION['cpanel']['username']); ?></p>
            <p class="text-gray-200">API Token: <?php echo htmlspecialchars($_SESSION['cpanel']['apiToken']); ?></p>
            <a href="?logout" class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 transition-all">Logout</a>
        </div>
    </div>

    <div class="flex-1 p-6">
        <?php if ($section === 'files'): ?>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">File Manager</h2>
                    <div class="flex gap-3">
                        <form method="post" enctype="multipart/form-data" class="inline-flex">
                            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
                            <input type="file" name="file" id="file-upload" class="hidden" onchange="this.form.submit()">
                            <label for="file-upload" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/50 cursor-pointer flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v12m0-12l-4 4m4-4l4 4m-8 4h12"/>
                                </svg>
                                Upload
                            </label>
                        </form>
                        <button onclick="showFileModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-500/50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm6 10v6m-3-3h6"/>
                            </svg>
                            New File
                        </button>
                        <button onclick="showFolderModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-500/50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 5h6l3 3h9v11H3zm9 8h12m-6-6v12"/>
                            </svg>
                            New Folder
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-400">
                        <?php
                        $lastIndex = count($breadcrumb) - 1;
                        foreach ($breadcrumb as $index => $part) {
                            if ($index === $lastIndex) {
                                echo '<span class="text-blue-400">' . htmlspecialchars($part['name']) . '</span>';
                            } else {
                                echo '<a href="?section=files&dir=' . urlencode($part['path']) . '" class="text-gray-400 hover:text-blue-400">' . htmlspecialchars($part['name']) . '</a>';
                                echo ' / ';
                            }
                        }
                        ?>
                    </p>
                </div>

                <?php if (isset($uploadMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($uploadMessage); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($createMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($createMessage); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($editMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($editMessage); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($deleteMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($deleteMessage); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($renameMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($renameMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-600">
                                <th class="p-4">Name</th>
                                <th class="p-4">Size</th>
                                <th class="p-4">Modified</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($contents['data'])) {
                                foreach ($contents['data'] as $item) {
                                    echo '<tr class="file-item border-b border-gray-600">';
                                    echo '<td class="p-4 text-gray-200">';
                                    if ($item['type'] === 'dir') {
                                        echo '📁 <a href="?section=files&dir=' . urlencode($currentDir . '/' . $item['file']) . '" class="text-gray-200 hover:text-blue-400">' . htmlspecialchars($item['file']) . '</a>';
                                    } else {
                                        echo '📄 <span onclick="showViewModal(\'' . htmlspecialchars($item['file'], ENT_QUOTES) . '\')" class="cursor-pointer hover:text-blue-400">' . htmlspecialchars($item['file']) . '</span>';
                                    }
                                    echo '</td>';
                                    echo '<td class="p-4 text-gray-400">' . (isset($item['size']) ? htmlspecialchars($item['size']) : '-') . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . (isset($item['mtime']) ? date('Y-m-d H:i:s', $item['mtime']) : '-') . '</td>';
                                    echo '<td class="p-4">';
                                    echo '<div class="flex gap-3">';
                                    if ($item['type'] !== 'dir') {
                                        echo '<button onclick="showEditModal(\'' . htmlspecialchars($item['file'], ENT_QUOTES) . '\')" class="text-green-400 hover:text-green-300 transition-colors">Edit</button>';
                                        echo '<button onclick="showRenameModal(\'' . htmlspecialchars($item['file'], ENT_QUOTES) . '\', \'file\')" class="text-yellow-400 hover:text-yellow-300 transition-colors">Rename</button>';
                                        echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this file?\');" class="inline">';
                                        echo '<input type="hidden" name="action" value="delete_file">';
                                        echo '<input type="hidden" name="filename" value="' . htmlspecialchars($item['file']) . '">';
                                        echo '<button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Delete</button>';
                                        echo '</form>';
                                    } else {
                                        echo '<button onclick="showRenameModal(\'' . htmlspecialchars($item['file'], ENT_QUOTES) . '\', \'dir\')" class="text-yellow-400 hover:text-yellow-300 transition-colors">Rename</button>';
                                        echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this folder?\');" class="inline">';
                                        echo '<input type="hidden" name="action" value="delete_folder">';
                                        echo '<input type="hidden" name="foldername" value="' . htmlspecialchars($item['file']) . '">';
                                        echo '<button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Delete</button>';
                                        echo '</form>';
                                    }
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="p-4 text-center text-gray-400">No files found or error.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($section === 'ftp'): ?>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">FTP Manager</h2>
                    <div class="flex gap-3">
                        <button onclick="showFTPModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-500/50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v12m0-12l-4 4m4-4l4 4m-8 4h12"/>
                            </svg>
                            New FTP Account
                        </button>
                    </div>
                </div>

                <?php if (!empty($ftpMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($ftpMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-600">
                                <th class="p-4">Username</th>
                                <th class="p-4">Directory</th>
                                <th class="p-4">Quota</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($ftpAccounts['data'])) {
                                foreach ($ftpAccounts['data'] as $account) {
                                    echo '<tr class="file-item border-b border-gray-600">';
                                    echo '<td class="p-4 text-gray-200">' . htmlspecialchars($account['user']) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($account['homedir']) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars(isset($account['diskquota']) ? ($account['diskquota'] === 'unlimited' ? 'Unlimited' : $account['diskquota'] . ' MB') : 'N/A') . '</td>';
                                    echo '<td class="p-4">';
                                    echo '<div class="flex gap-3">';
                                    echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this FTP account?\');" class="inline">';
                                    echo '<input type="hidden" name="action" value="delete_ftp">';
                                    echo '<input type="hidden" name="user" value="' . htmlspecialchars($account['user']) . '">';
                                    echo '<input type="hidden" name="destroy" value="0">';
                                    echo '<button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Delete</button>';
                                    echo '</form>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="p-4 text-center text-gray-400">No FTP accounts found or error.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($section === 'dns'): ?>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">DNS/Subdomain Manager</h2>
                    <div class="flex gap-3">
                        <button onclick="showAddDNSModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v12m0-12l-4 4m4-4l4 4m-8 4h12"/>
                            </svg>
                            Add DNS Record
                        </button>
                        <button onclick="showSubdomainModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-500/50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v12m0-12l-4 4m4-4l4 4m-8 4h12"/>
                            </svg>
                            Add Subdomain
                        </button>
                    </div>
                </div>

                <?php if (!empty($dnsMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($dnsMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-600">
                                <th class="p-4">Line Index</th>
                                <th class="p-4">Name</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">TTL</th>
                                <th class="p-4">Data</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($dnsRecords['data'])) {
                                foreach ($dnsRecords['data'] as $record) {
                                    $lineIndex = isset($record['line_index']) ? $record['line_index'] : (isset($record['line']) ? $record['line'] : 'N/A');
                                    $dname = isset($record['dname_b64']) ? base64_decode($record['dname_b64']) : (isset($record['name']) ? $record['name'] : 'N/A');
                                    $data = [];
                                    if (isset($record['data_b64']) && is_array($record['data_b64'])) {
                                        $data = array_map('base64_decode', $record['data_b64']);
                                    } elseif (isset($record['data']) && is_array($record['data'])) {
                                        $data = $record['data'];
                                    } else {
                                        $data = ['N/A'];
                                    }
                                    $recordType = isset($record['record_type']) ? $record['record_type'] : (isset($record['type']) ? $record['type'] : 'N/A');
                                    $ttl = isset($record['ttl']) ? $record['ttl'] : 'N/A';
                                    $dataString = is_array($data) ? implode(', ', $data) : $data;
                                    $maxLength = 50;
                                    $displayData = $dataString;
                                    if (strlen($dataString) > $maxLength) {
                                        $displayData = substr($dataString, 0, $maxLength) . '...';
                                    }
                                    echo '<tr class="file-item border-b border-gray-600">';
                                    echo '<td class="p-4 text-gray-200">' . htmlspecialchars($lineIndex) . '</td>';
                                    echo '<td class="p-4 text-gray-200">' . htmlspecialchars($dname) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($recordType) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($ttl) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($displayData) . '</td>';
                                    echo '<td class="p-4">';
                                    echo '<div class="flex gap-3">';
                                    echo '<button onclick="showDNSModal(\'' . htmlspecialchars($lineIndex, ENT_QUOTES) . '\', \'' . htmlspecialchars($dname, ENT_QUOTES) . '\', \'' . htmlspecialchars($recordType, ENT_QUOTES) . '\', \'' . htmlspecialchars($ttl, ENT_QUOTES) . '\', \'' . htmlspecialchars($dataString, ENT_QUOTES) . '\')" class="text-green-400 hover:text-green-300 transition-colors">Edit</button>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="p-4 text-center text-gray-400">No DNS records found or error.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($section === 'modsec'): ?>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">ModSecurity Manager</h2>
                    <div class="flex gap-3">
                        <form method="post" onsubmit="return confirm('Are you sure you want to disable ModSecurity for all domains?');">
                            <input type="hidden" name="action" value="disable_all_modsec">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.36 6.64a9 9 0 11-12.73 0M12 2a10 10 0 1010 10"/>
                                </svg>
                                Disable All
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($modsecMessage)): ?>
                    <div class="mb-6 p-4 bg-blue-900/50 text-blue-200 rounded-lg">
                        <?php echo htmlspecialchars($modsecMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-600">
                                <th class="p-4">Domain</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Dependencies</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($modsecDomains['data'])) {
                                foreach ($modsecDomains['data'] as $domain) {
                                    echo '<tr class="file-item border-b border-gray-600">';
                                    echo '<td class="p-4 text-gray-200">' . htmlspecialchars($domain['domain']) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($domain['type']) . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . ($domain['enabled'] ? 'Enabled' : 'Disabled') . '</td>';
                                    echo '<td class="p-4 text-gray-400">' . htmlspecialchars($domain['searchhint']) . '</td>';
                                    echo '<td class="p-4">';
                                    echo '<div class="flex gap-3">';
                                    if ($domain['enabled']) {
                                        echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to disable ModSecurity for this domain?\');" class="inline">';
                                        echo '<input type="hidden" name="action" value="disable_modsec">';
                                        echo '<input type="hidden" name="domain" value="' . htmlspecialchars($domain['domain']) . '">';
                                        echo '<button type="submit" class="text-red-400 hover:text-red-300 transition-colors">Disable</button>';
                                        echo '</form>';
                                    } else {
                                        echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to enable ModSecurity for this domain?\');" class="inline">';
                                        echo '<input type="hidden" name="action" value="enable_modsec">';
                                        echo '<input type="hidden" name="domain" value="' . htmlspecialchars($domain['domain']) . '">';
                                        echo '<button type="submit" class="text-green-400 hover:text-green-300 transition-colors">Enable</button>';
                                        echo '</form>';
                                    }
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" class="p-4 text-center text-gray-400">No domains found or error.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create File Modal -->
        <div id="createFileModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Create New File</h3>
                    <button onclick="hideFileModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="create_file">
                    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">File Name</label>
                        <input type="text" name="filename" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="example.txt" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Content (Optional)</label>
                        <textarea name="content" rows="5" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideFileModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Folder Modal -->
        <div id="createFolderModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Create New Folder</h3>
                    <button onclick="hideFolderModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="create_folder">
                    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Folder Name</label>
                        <input type="text" name="foldername" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="new_folder" required>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideFolderModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit File Modal -->
        <div id="editFileModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-2xl w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Edit File</h3>
                    <button onclick="hideEditModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="edit_file">
                    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
                    <input type="hidden" id="edit_filename" name="filename">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">File Name</label>
                        <input type="text" id="edit_filename_display" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Content</label>
                        <textarea id="edit_content" name="content" rows="10" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideEditModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View File Modal -->
        <div id="viewFileModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-2xl w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">View File</h3>
                    <button onclick="hideViewModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">File Name</label>
                    <input type="text" id="view_filename" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white" readonly>
                </div>
                <div class="mt-4">
                    <label class="block text-gray-300 mb-2 font-medium">Content</label>
                    <pre id="view_content" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white max-h-96 overflow-auto"></pre>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button onclick="hideViewModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Close</button>
                </div>
            </div>
        </div>

        <!-- Rename Modal -->
        <div id="renameModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Rename</h3>
                    <button onclick="hideRenameModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="rename">
                    <input type="hidden" id="rename_type" name="type">
                    <input type="hidden" id="rename_old_name" name="old_name">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Current Name</label>
                        <input type="text" id="rename_old_name_display" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">New Name</label>
                        <input type="text" id="rename_new_name" name="new_name" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideRenameModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Rename</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create FTP Account Modal -->
        <div id="createFTPModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Create FTP Account</h3>
                    <button onclick="hideFTPModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="create_ftp">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Username</label>
                        <input type="text" name="login" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="ftp_user" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Password</label>
                        <input type="password" name="password" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Quota</label>
                        <select name="quota" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="unlimited">Unlimited</option>
                            <option value="100">100 MB</option>
                            <option value="500">500 MB</option>
                            <option value="1000">1 GB</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Home Directory</label>
                        <input type="text" name="homedir" value="<?php echo htmlspecialchars($currentDir); ?>" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideFTPModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Subdomain Modal -->
        <div id="addSubdomainModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Add Subdomain</h3>
                    <button onclick="hideSubdomainModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="add_subdomain">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Subdomain</label>
                        <input type="text" name="subdomain" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="sub" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Document Root (Optional)</label>
                        <input type="text" name="dir" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="/home/username/public_html/sub">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideSubdomainModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add DNS Record Modal -->
        <div id="addDNSModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Add DNS Record</h3>
                    <button onclick="hideAddDNSModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="add_dns">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Name</label>
                        <input type="text" name="dname" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" placeholder="sub or @" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Type</label>
                        <select name="record_type" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="A">A</option>
                            <option value="CNAME">CNAME</option>
                            <option value="MX">MX</option>
                            <option value="TXT">TXT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Address</label>
                        <input type="text" name="address" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">TTL</label>
                        <input type="number" name="ttl" value="14400" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideAddDNSModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit DNS Record Modal -->
        <div id="editDNSModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center">
            <div class="bg-gray-800 rounded-xl p-8 max-w-lg w-full modal">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Edit DNS Record</h3>
                    <button onclick="hideDNSModal()" class="text-gray-400 hover:text-gray-200 text-2xl">×</button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="edit_dns">
                    <input type="hidden" id="dns_line_index" name="line_index">
                    <input type="hidden" id="dns_serial" name="serial" value="<?php echo htmlspecialchars($serialNumber); ?>">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Name</label>
                        <input type="text" id="dns_dname" name="dname" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Type</label>
                        <input type="text" id="dns_record_type" name="record_type" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">TTL</label>
                        <input type="number" id="dns_ttl" name="ttl" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Data</label>
                        <input type="text" id="dns_data" name="data" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideDNSModal()" class="px-5 py-2.5 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showFileModal() {
            document.getElementById('createFileModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#createFileModal .modal').classList.add('show'), 10);
        }

        function hideFileModal() {
            const modal = document.querySelector('#createFileModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('createFileModal').classList.add('hidden'), 300);
        }

        function showFolderModal() {
            document.getElementById('createFolderModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#createFolderModal .modal').classList.add('show'), 10);
        }

        function hideFolderModal() {
            const modal = document.querySelector('#createFolderModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('createFolderModal').classList.add('hidden'), 300);
        }

        function showEditModal(filename) {
            fetch(`?section=files&view=${encodeURIComponent(filename)}&dir=${encodeURIComponent('<?php echo $currentDir; ?>')}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('edit_filename').value = filename;
                    document.getElementById('edit_filename_display').value = filename;
                    document.getElementById('edit_content').value = data;
                    document.getElementById('editFileModal').classList.remove('hidden');
                    setTimeout(() => document.querySelector('#editFileModal .modal').classList.add('show'), 10);
                });
        }

        function hideEditModal() {
            const modal = document.querySelector('#editFileModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('editFileModal').classList.add('hidden'), 300);
        }

        function showViewModal(filename) {
            fetch(`?section=files&view=${encodeURIComponent(filename)}&dir=${encodeURIComponent('<?php echo $currentDir; ?>')}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('view_filename').value = filename;
                    document.getElementById('view_content').textContent = data;
                    document.getElementById('viewFileModal').classList.remove('hidden');
                    setTimeout(() => document.querySelector('#viewFileModal .modal').classList.add('show'), 10);
                });
        }

        function hideViewModal() {
            const modal = document.querySelector('#viewFileModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('viewFileModal').classList.add('hidden'), 300);
        }

        function showRenameModal(name, type) {
            document.getElementById('rename_old_name').value = name;
            document.getElementById('rename_old_name_display').value = name;
            document.getElementById('rename_new_name').value = name;
            document.getElementById('rename_type').value = type;
            document.getElementById('renameModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#renameModal .modal').classList.add('show'), 10);
        }

        function hideRenameModal() {
            const modal = document.querySelector('#renameModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('renameModal').classList.add('hidden'), 300);
        }

        function showFTPModal() {
            document.getElementById('createFTPModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#createFTPModal .modal').classList.add('show'), 10);
        }

        function hideFTPModal() {
            const modal = document.querySelector('#createFTPModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('createFTPModal').classList.add('hidden'), 300);
        }

        function showSubdomainModal() {
            document.getElementById('addSubdomainModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#addSubdomainModal .modal').classList.add('show'), 10);
        }

        function hideSubdomainModal() {
            const modal = document.querySelector('#addSubdomainModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('addSubdomainModal').classList.add('hidden'), 300);
        }

        function showAddDNSModal() {
            document.getElementById('addDNSModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#addDNSModal .modal').classList.add('show'), 10);
        }

        function hideAddDNSModal() {
            const modal = document.querySelector('#addDNSModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('addDNSModal').classList.add('hidden'), 300);
        }

        function showDNSModal(line_index, dname, record_type, ttl, data) {
            document.getElementById('dns_line_index').value = line_index;
            document.getElementById('dns_dname').value = dname;
            document.getElementById('dns_record_type').value = record_type;
            document.getElementById('dns_ttl').value = ttl;
            document.getElementById('dns_data').value = data;
            document.getElementById('editDNSModal').classList.remove('hidden');
            setTimeout(() => document.querySelector('#editDNSModal .modal').classList.add('show'), 10);
        }

        function hideDNSModal() {
            const modal = document.querySelector('#editDNSModal .modal');
            modal.classList.remove('show');
            setTimeout(() => document.getElementById('editDNSModal').classList.add('hidden'), 300);
        }
    </script>
</body>
</html>
