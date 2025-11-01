# shellcheck shell=bash

function _get_credential {
  local credential="${1}"
  local session="${2}"

  echo "${session}" | jq -rec ".Credentials.${credential}"
}

function _aws_login {
  # AWS STS args
  local username="${1}"
  local mfa_token="${2}"
  local duration="${3}"
  local account="${4}"

  # Session variables
  local session
  export AWS_ACCESS_KEY_ID
  export AWS_SECRET_ACCESS_KEY
  export AWS_SESSION_TOKEN

  echo "[INFO] Logging in to AWS as '${username}'."
  session=$(aws sts get-session-token --duration-seconds ${duration} --serial-number arn:aws:iam::${account}:mfa/${username} --token-code ${mfa_token})

  AWS_ACCESS_KEY_ID="$(_get_credential "AccessKeyId" "${session}")"
  AWS_SECRET_ACCESS_KEY="$(_get_credential "SecretAccessKey" "${session}")"
  AWS_SESSION_TOKEN="$(_get_credential "SessionToken" "${session}")"
}

function main {
    export AWS_PROFILE=default

    echo "Hi, Please add your AWS account id"
    read ACCOUNT_ID

    echo "Please add your IAM username"
    read MFA_USER

    echo "Please add your 6-digit code from your authenticator app"
    read MFA_TOKEN

    _aws_login $MFA_USER $MFA_TOKEN 3600 $ACCOUNT_ID

    export AWS_PROFILE=mfa
    echo "default profile is "$AWS_PROFILE
}

main "$@"