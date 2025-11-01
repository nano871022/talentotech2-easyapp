config {
  plugin_dir = ".tflint.d/plugins"
}

plugin "aws" {
  enabled = true
  version = "0.38.0"
  source  = "github.com/terraform-linters/tflint-ruleset-aws"
}

plugin "opa" {
  enabled = true
  version = "0.9.0"
  source  = "github.com/terraform-linters/tflint-ruleset-opa"
}

