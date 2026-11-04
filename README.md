# BackgroundMusic

Schedule background music for players on Axolotl-PM servers

[![CI](https://github.com/NhanAZ-Plugins/BackgroundMusic/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/BackgroundMusic/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/BackgroundMusic)](https://github.com/NhanAZ-Plugins/BackgroundMusic/releases/latest)
[![License](https://img.shields.io/github/license/NhanAZ-Plugins/BackgroundMusic)](https://github.com/NhanAZ-Plugins/BackgroundMusic/blob/master/LICENSE)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)


## Current status

The manifest declares plugin API `5.0.0`. CI checks PHPStan max against the declared minimum and a pinned Axolotl-PM 5.x source, builds the PHAR, and verifies that it contains no bundled resource pack or audio. The sound packet and server startup passed the compatibility workflow; whether a particular retail client plays music still depends on that client's vanilla assets and settings.

The old `libRegRsp` resource-pack integration is no longer needed. The plugin does not request a custom pack.

## Behavior and limitations

- A join queues the first `PlaySoundPacket` for the scheduler heartbeat, then repeats it every 4,400 ticks, about 220 seconds at 20 ticks per second.
- Each packet uses the vanilla `music.game` event; the client chooses the built-in music for its game version and settings.
- The packet uses the player's current position when each sound is sent.
- Rejoining replaces the previous task for that player. Quit, offline detection, and plugin disable cancel scheduled playback.
- There are no commands or permissions in `plugin.yml`.

The event name is present in Mojang's [Bedrock sample music definitions](https://github.com/Mojang/bedrock-samples/blob/main/resource_pack/sounds/music_definitions.json). Packet delivery and the Axolotl-PM server startup are covered by the checked workflows.

## Building and installation

The [build workflow](.github/workflows/build.yml) pins [DevTools](https://github.com/NhanAZ/DevTools), analyzes the source, builds a PHAR, and verifies its contents. The project no longer distributes C418 audio. Keep any test server and world separate from production data.

The local playback lifecycle fixture runs with `php tests/scheduler-lifecycle.php`. It checks task replacement, cancellation, and packet position without a connected player. The [build workflow](.github/workflows/build.yml) also runs it before PHPStan and PHAR validation.

## Audio and license

The root [GPL-3.0 license text](LICENSE) applies to the plugin source. No third-party recording or custom music pack is included in the project artifact.

For project issues, use [GitHub Issues](https://github.com/NhanAZ-Plugins/BackgroundMusic/issues). For community support, visit [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
