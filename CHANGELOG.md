# CHANGELOG

## [1.2.0] - Unreleased

### Added
- Added support for automatically joining new users into the guild.

### Fixed
- Fixed several type errors.
- Fixed a bug where a member could be added multiple times if they associate their discord twice or more times.

## [1.2.0 Alpha 2] - 2017-08-01

### Fixed
- Fixed a type error when trying to edit an forum.

## [1.2.0 Alpha 1] - 2017-07-31

### Changed
- Changed the how we communicate with the discord API, switched to using the [RestCord](https://restcord.com) library instead of our own implementation.

### Removed
- Removed support for name syncing for now.

## [1.1.0 Beta 3] - 2017-03-14

### Changed
- Changed the handling when we cannot reach https://discordapp.com, in that case we just move on instead of throwing an error.

## [1.1.0 Beta 2] - 2017-03-08

### Fixed
- Fixed a bug where not all required attributes would be created.

## [1.1.0 Beta 1] - 2017-03-06

### Added
- Added support for sending notifications to discord about a new file (IPS' downloads app).
- Added support for sending notifications to discord about a new calendar event (IPS' downloads app).

### Changes
- Changed the default values of some column to `NULL` instead of a blank value. Thanks to [MADMAN32395](https://github.com/madman32395)

### Removed
- Removed unused methods. Thanks to [MADMAN32395](https://github.com/madman32395)

## [1.0.0 Beta 6] - 2017-03-01

### Added
- Added logging for discord related exceptions.

### Fixed
- Fixed a file permission error affecting the `auth.php` file.

## [1.0.0 Beta 5] - 2017-02-11

### Changed
- Changed the handling of Discord users that are not found, they will be just ignored now instead of throwing an error.

## [1.0.0 Beta 4] - 2017-02-09

### Fixed
- Fixed a bug where syncing groups would not work.

## [1.0.0 Beta 3] - 2017-02-08

### Added
- Added support for immediately syncing the groups after establishing a link between an IPS account and an discord account.

### Fixed
- Fixed a bug where syncing groups would not work.

## [1.0.0 Beta 2] - 2017-02-06

### Added
- Added a new Setting: `Notify about (all) new topics?`.
- Added a new Setting: `Notify about new unapproved topics?`.
- Added a new Setting: `Notify about (all) new posts?`.
- Added a new Setting: `Notify about new unapproved posts?`.

### Fixed
- Fixed a bug where you would see a 500 Error Page in specific server configurations (wrong file permissions).
- Fixed a bug where a notification would be sent to discord about a new topic after editing an existing one.

## 1.0.0 Beta 1 - 2017-02-05

### Added
- Added support for posting messages to a discord channel when a new topic/post is created on IPS.
- Added support for syncing the IPS groups -> Discord roles.
- Added a task `syncGroups` to sync the IPS groups -> Discord roles.
- Added support for automatically banning/unbanning a member from discord when they are banned/unbanned from IPS.
- Added support for automatically removing a member from discord when they are removed from IPS.

[1.2.0]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.2.0-alpha.2...restcord-experimental
[1.2.0 Alpha 2]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.2.0-alpha.1...v1.2.0-alpha.2
[1.2.0 Alpha 1]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.1.0-beta.3...v1.2.0-alpha.1
[1.1.0 Beta 3]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.1.0-beta.2...v1.1.0-beta.3
[1.1.0 Beta 2]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.1.0-beta.1...v1.1.0-beta.2
[1.1.0 Beta 1]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.6...v1.1.0-beta.1
[1.0.0 Beta 6]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.5...v1.0.0-beta.6
[1.0.0 Beta 5]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.4...v1.0.0-beta.5
[1.0.0 Beta 4]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.3...v1.0.0-beta.4
[1.0.0 Beta 3]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.2...v1.0.0-beta.3
[1.0.0 Beta 2]: https://github.com/ABSAhmad/IpsDiscordIntegration/compare/v1.0.0-beta.1...v1.0.0-beta.2