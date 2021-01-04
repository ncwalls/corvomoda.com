# G Suite Auth Plugin for WordPress

This plugin allows for the creation and authentication of WordPress administrators using Makespace's G Suite account.

## Production Version

The plugin should be automatically included on all sites built with the Makespace Framework theme (v2 or higher). To install this on an older site, [download the .zip folder](https://bitbucket.org/makespaceweb/g-suite-auth-plugin/downloads/) and install it manually. Activate the plugin, log out and back in, and then delete the default Makespace user (if needed).

## Development Version

When you pull the repo, run:

```bash
composer install
```

When your work is complete, create a .zip folder of the required plugin files, add it to downloads, and swap out the version in the Makespace Framework.

The .zip folder must be named `makespace-login.zip`. If you zip the files on a mac, you can run `zip -d makespace-login.zip __MACOSX/\*` to remove any hidden files from the zip after it's created.
