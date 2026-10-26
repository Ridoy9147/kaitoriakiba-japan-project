Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$sourceDir = "D:\japan project\wordpress-theme\kaitoriakiba-theme"
$zipPath1 = "D:\japan project\kaitoriakiba-theme.zip"
$zipPath2 = "D:\japan project\wordpress-theme\kaitoriakiba-theme.zip"

function Build-Zip($targetPath) {
    if (Test-Path $targetPath) {
        Remove-Item $targetPath -Force
    }

    $zip = [System.IO.Compression.ZipFile]::Open($targetPath, [System.IO.Compression.ZipArchiveMode]::Create)

    # 1. Add root theme directory entry
    $zip.CreateEntry("kaitoriakiba-theme/") | Out-Null

    # 2. Add all subdirectories explicitly with trailing slash
    $dirs = Get-ChildItem -Path $sourceDir -Recurse -Directory
    foreach ($dir in $dirs) {
        if ($dir.FullName -notmatch '\\\.git' -and $dir.FullName -notmatch '\\node_modules' -and $dir.FullName -notmatch '\\\.claude') {
            $relDir = $dir.FullName.Substring($sourceDir.Length + 1).Replace("\", "/") + "/"
            $entryDirName = "kaitoriakiba-theme/" + $relDir
            $zip.CreateEntry($entryDirName) | Out-Null
        }
    }

    # 3. Add style.css first to guarantee fast header detection
    $styleFile = Join-Path $sourceDir "style.css"
    if (Test-Path $styleFile) {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $styleFile, "kaitoriakiba-theme/style.css", [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
    }

    # 4. Add all other files with forward slashes
    $files = Get-ChildItem -Path $sourceDir -Recurse -File
    foreach ($file in $files) {
        if ($file.Name -ne "style.css" -and $file.FullName -notmatch '\\\.git' -and $file.FullName -notmatch '\\node_modules' -and $file.FullName -notmatch '\\\.claude') {
            $relFile = $file.FullName.Substring($sourceDir.Length + 1).Replace("\", "/")
            $entryFileName = "kaitoriakiba-theme/" + $relFile
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $entryFileName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
        }
    }

    $zip.Dispose()
    Write-Host "Created: $targetPath (Size: $((Get-Item $targetPath).Length) bytes)"
}

Build-Zip $zipPath1
Build-Zip $zipPath2
