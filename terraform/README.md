# Terraform AWS Infrastructure

A repository for managing AWS infrastructure using Terraform with Nix-based development environment.

## Overview

This project provides a structured approach to deploying and managing AWS resources using Terraform, with integrated MFA authentication, state management, and linting capabilities.

## Prerequisites

- **Nix** with flakes enabled
- **direnv** (optional but recommended)
- **AWS account** with IAM credentials
- **MFA device** configured for your IAM user

## Features

- 🔧 **Nix Flakes**: Reproducible development environment
- 🔐 **MFA Authentication**: Secure AWS login with session token management
- 📦 **Remote State**: S3 backend with encryption and locking
- ✅ **Linting**: Integrated TFLint with AWS and OPA rulesets
- 🔄 **Automated Setup**: direnv integration for automatic environment loading

## Getting Started

### 1. Environment Setup

#### Option A: Using direnv (Recommended)

If you have `direnv` installed, simply navigate to the project directory:

```bash
cd terraform_aws
direnv allow
```

This will automatically:
- Load the Nix development environment
- Source the AWS login script

#### Option B: Manual Nix Shell

```bash
nix develop
```

### 2. AWS Authentication

The project includes a helper script for AWS MFA authentication:

```bash
source ./aws_login.sh
```

You'll be prompted for:
- AWS Account ID
- IAM Username
- 6-digit MFA token from your authenticator app

The script will generate temporary session credentials (valid for 1 hour) and set the appropriate environment variables.

## Development Environment

The Nix flake provides the following tools:

- **Terraform** (~> 1.0)
- **AWS CLI**
- **Python 3.13**
- **jq** (JSON processor)

### Installed Terraform Providers

- `hashicorp/aws` (v6.18.0)

### Backend Configuration

Terraform state is stored in an S3 backend with:
- **Bucket**: `repository-terraform-states-prod`
- **Key**: `repository.tfstate`
- **Region**: `us-east-1`
- **Encryption**: Enabled
- **State Locking**: Enabled

## Usage

### Initialize Terraform

```bash
terraform init
```

### Plan Changes

```bash
terraform plan
```

### Apply Changes

```bash
terraform apply
```

### Destroy Resources

```bash
terraform destroy
```

## Code Quality

### TFLint

The project uses TFLint with the following plugins:

- **AWS Plugin** (v0.38.0): AWS-specific linting rules
- **OPA Plugin** (v0.9.0): Policy as Code validation

To run TFLint:

```bash
tflint
```

To initialize TFLint plugins:

```bash
tflint --init
```

## Project Structure

```
.
├── aws_login.sh       # AWS MFA authentication helper script
├── flake.nix          # Nix flake configuration
├── flake.lock         # Nix flake lock file
├── main.tf            # Terraform configuration
├── .envrc             # direnv configuration
├── .tflint.hcl        # TFLint configuration
└── README.md          # This file
```

## AWS Credentials

### Session Token Flow

1. Run `source ./aws_login.sh`
2. Provide your AWS account ID, IAM username, and MFA token
3. The script calls `aws sts get-session-token` with your MFA device
4. Temporary credentials are exported as environment variables:
   - `AWS_ACCESS_KEY_ID`
   - `AWS_SECRET_ACCESS_KEY`
   - `AWS_SESSION_TOKEN`
5. AWS profile is set to `mfa`

### Token Expiration

Session tokens expire after 1 hour (3600 seconds). When your session expires, re-run the login script.

## Security Best Practices

- ✅ Never commit AWS credentials to version control
- ✅ Always use MFA for production accounts
- ✅ Encrypt Terraform state files (enabled by default)
- ✅ Use state locking to prevent concurrent modifications
- ✅ Regularly rotate IAM access keys
- ✅ Review Terraform plans before applying

## Troubleshooting

### "No valid credential sources found"

- Ensure you've run `source ./aws_login.sh` and provided valid credentials
- Check that your MFA token is current (they expire every 30 seconds)

### "Error loading state"

- Verify you have access to the S3 bucket `repository-terraform-states-prod`
- Ensure your AWS credentials have the necessary S3 permissions

### "Provider configuration not present"

- Run `terraform init` to download required providers

## Contributing

When adding new infrastructure:

1. Create feature branches for changes
2. Run `terraform fmt` to format code
3. Run `tflint` to validate changes
4. Create a plan and review carefully
5. Apply changes after review

## License

[Add your license information here]

## Support

[Add your support/contact information here]

