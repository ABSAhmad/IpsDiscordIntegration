# Discord Integration

This application allows you to integrate discord into your forum in a few different ways.

* Provides a login handler for discord, so your members can login/signup through discord and establish a link this way.
* If a member has their discord account linked with their forum account, their groups will be synced to discord roles.
* Post notifications to discord about certain events that take place in your forum.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

You will need to have a working IPS install and the development mode MUST be enabled.

### Installing

You will need to clone the repo into a folder named `./applications/discord`.

```
# Given you are in the IPS root (on the terminal), you can do the following:
git clone git@github.com:ABSAhmad/IpsDiscordIntegration.git ./applications/discord
```

Make sure that you have [composer](https://getcomposer.org/) installed.

Then install the package dependencies using composer:

```
# This assumes that you have installed composer globally on your machine already.
composer install
```

## Deployment

You can download the latest version from [GitHub](https://github.com/ABSAhmad/IpsDiscordIntegration/releases).
The .tar files already contain all dependencies and can be installed using the standard ACP tools provided by IPS.

## Built With

* [RestCord](https://restcord.com/) - Used to communicate with Discord.

## Code of Conduct

Please read our [CODE_OF_CONDUCT.md](https://github.com/ABSAhmad/IpsDiscordIntegration/blob/master/CODE_OF_CONDUCT.md) before contributing.

## Contributing

Please read [CONTRIBUTING.md](https://github.com/ABSAhmad/IpsDiscordIntegration/blob/master/CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## Authors

* **Ahmad El-Bardan** - [ABSAhmad](https://github.com/ABSAhmad)

See also the list of [contributors](https://github.com/ABSAhmad/IpsDiscordIntegration/contributors) who participated in this project.

## License

This project is licensed under the GNUv3 License - see the [LICENSE](https://github.com/ABSAhmad/IpsDiscordIntegration/blob/master/LICENSE) file for details
