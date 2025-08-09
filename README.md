# Artisan CLI
A powerful and extensible command-line tool for the Artisan Framework.

## Install
To install the Artisan CLI globally, simply download the latest PHAR release, make it executable, and move it to a directory in your system's PATH:

## Ubuntu
```bash
curl -LO https://github.com/artisanfw/cli/releases/latest/download/artisan.phar
chmod +x artisan.phar
sudo mv artisan.phar /usr/local/bin/artisan
```
After this, you can run the CLI using the artisan command from anywhere in your terminal.

**Usage:**
```bash
artisan new <project-folder> [--version=major.minor] [--dev] [--with-users]
```
* `project-folder`: Name of the folder where the new project will be created.
* `--version`: (Optional) Specify a version of the starter to install.
* `--dev`: Install the latest development version.
* `--with-users`: Use the Artisan package with pre-installed Account management features.

More commands and extensions will be added in future releases.


