# BackgroundMusic

BackgroundMusic is an experimental Axolotl-PM plugin that schedules a custom music sound packet for each player who joins a server. The source includes a resource pack containing C418's "Sweden" recording.

## Current status

The manifest declares plugin API `5.0.0`. The minimum compatible server version and a supported Axolotl-PM range have not been verified. There is no validated DevTools build, PHPStan max result, server runtime test, or GitHub release for the current source.

The historical [Poggit manifest](.poggit.yml) references `libBedrock` 1.1.7 for resource pack registration. That dependency has not been migrated or verified for this plugin. Do not treat the source checkout as an installable release.

## Behavior and limitations

- A join queues the first `PlaySoundPacket` for the scheduler heartbeat, then repeats it every 4,400 ticks, about 220 seconds at 20 ticks per second.
- The packet uses the player's position captured at join. It does not track later movement.
- Each join schedules another task. The current code checks whether the player is online but does not cancel that task after a disconnect, so reconnects can accumulate tasks.
- There are no commands or permissions in `plugin.yml`.

The sound identifier comes from the [resource pack definition](resources/BackgroundMusic%20Pack/sounds/sound_definitions.json). Playback on a connected Bedrock client has not been verified.

## Building and installation

This repository has no verified installable PHAR. Before a build or release, the plugin needs a compatible resource pack integration, PHPStan at maximum level, a validated PHAR built with [DevTools](https://github.com/NhanAZ/DevTools), and a server test with a connected client. Keep any test server and world separate from production data.

## Audio and license

The root [GPL-3.0 license text](LICENSE) applies to the plugin source. It does not establish permission to redistribute the bundled `C418Sweden.ogg` recording. The [resource pack notice](resources/BackgroundMusic%20Pack/sounds/LICENSE) points to a C418 permission page that currently returns 404. A link to a free download or a purchase page is not a redistribution grant. Resolve the audio rights or use a recording with a verified compatible license before publishing a PHAR or resource pack.

Music credit: C418, "Sweden", *Minecraft Volume Alpha*. The [album is available from C418](https://c418.bandcamp.com/album/minecraft-volume-alpha).

For project issues, use [GitHub Issues](https://github.com/NhanAZ-Plugins/BackgroundMusic/issues). For community support, visit [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
