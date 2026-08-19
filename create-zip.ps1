$sourceDir = $PSScriptRoot
$zipFile = Join-Path $sourceDir "azadnews-photo-card.zip"
$stageDir = Join-Path $sourceDir "azadnews_pack_stage\azadnews-photo-card"

if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
}
if (Test-Path (Join-Path $sourceDir "azadnews_pack_stage")) {
    Remove-Item (Join-Path $sourceDir "azadnews_pack_stage") -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $stageDir | Out-Null

Copy-Item "$sourceDir\azadnews-photo-card.php" "$stageDir\"
Copy-Item "$sourceDir\readme.txt" "$stageDir\"
Copy-Item "$sourceDir\includes" "$stageDir\" -Recurse
Copy-Item "$sourceDir\templates" "$stageDir\" -Recurse
Copy-Item "$sourceDir\assets" "$stageDir\" -Recurse

Compress-Archive -Path $stageDir -DestinationPath $zipFile -Force
Remove-Item (Join-Path $sourceDir "azadnews_pack_stage") -Recurse -Force

Write-Host "Plugin zip successfully created at $zipFile"

