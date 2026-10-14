# BackgroundMusic

BackgroundMusic is an experimental Axolotl-PM plugin that schedules a custom music sound packet for each player who joins a server. The source includes a resource pack containing C418's "Sweden" recording.

## Current status

The manifest declares plugin API `5.0.0`. The current source passes PHPStan max against both Axolotl-PM source revisions pinned in the [build workflow](.github/workflows/build.yml). A local PHAR built with DevTools loaded and registered its pack on an isolated Axolotl-PM 5.49.1 server. A headless Bedrock client joined and received a `PlaySoundPacket` named `C418Sweden` with volume and pitch set to 1. Other runtime versions, audible playback on a real client, and a supported version range remain unverified. There is no published GitHub release.

The current source uses [libRegRsp](https://github.com/NhanAZ-Libraries/libRegRsp) to compile and register the resource pack. The [DevTools dependency declaration](devtools.yml) specifies the virion range. The [previous Poggit manifest](https://github.com/NhanAZ-Plugins/BackgroundMusic/blob/77dd77fc0386a781e41c52c4374a5d8d73359f01/.poggit.yml) referenced `libBedrock` 1.1.7. It was retired because that build path no longer matches the source. Do not treat the source checkout as an installable release.

The plugin requires players to accept the resource pack. On Axolotl-PM API builds without a runtime setter for this requirement, set `force_resources: true` in `resource_packs/resource_packs.yml` before starting the server.

## Behavior and limitations

- A join queues the first `PlaySoundPacket` for the scheduler heartbeat, then repeats it every 4,400 ticks, about 220 seconds at 20 ticks per second.
- The packet uses the player's current position when each sound is sent.
- Rejoining replaces the previous task for that player. Quit, offline detection, and plugin disable cancel scheduled playback.
- There are no commands or permissions in `plugin.yml`.

The sound identifier comes from the [resource pack definition](resources/BackgroundMusic%20Pack/sounds/sound_definitions.json). Packet delivery to a connected headless client has been verified. Audio output and resource pack rendering have not been verified.

## Building and installation

This repository has no published PHAR. The [build workflow](.github/workflows/build.yml) pins [DevTools](https://github.com/NhanAZ/DevTools) and its inputs to analyze the source, build a PHAR, and verify its contents. The workflow does not upload or release the PHAR while audio redistribution rights remain unresolved. A real client audio playback test and rights review are still required before release. Keep any test server and world separate from production data.

The local playback lifecycle fixture runs with `php tests/scheduler-lifecycle.php`. It checks task replacement, cancellation, and packet position without a connected player. The [build workflow](.github/workflows/build.yml) also runs it before PHPStan and PHAR validation.

## Audio and license

The root [GPL-3.0 license text](LICENSE) applies to the plugin source. It does not establish permission to redistribute the bundled `C418Sweden.ogg` recording. The [resource pack notice](resources/BackgroundMusic%20Pack/sounds/LICENSE) points to a C418 permission page that currently returns 404. The official [C418 Bandcamp listing for “Sweden”](https://c418.bandcamp.com/track/sweden) labels the track “all rights reserved.” A free download or purchase page does not grant redistribution rights. Resolve the audio rights or use a recording with a verified compatible license before publishing a PHAR or resource pack.

Music credit: C418, "Sweden", *Minecraft Volume Alpha*. The [album is available from C418](https://c418.bandcamp.com/album/minecraft-volume-alpha).

For project issues, use [GitHub Issues](https://github.com/NhanAZ-Plugins/BackgroundMusic/issues). For community support, visit [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
