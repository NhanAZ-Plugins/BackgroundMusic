# BackgroundMusic

BackgroundMusic is an experimental Axolotl-PM plugin that periodically asks Bedrock clients to play the built-in `music.game` event after a player joins. It uses the client's installed vanilla sounds and does not bundle audio or a resource pack.

## Current status

The manifest declares plugin API `5.0.0`. CI checks PHPStan max against the declared minimum and a pinned Axolotl-PM 5.x source, builds the PHAR, and verifies that it contains no bundled resource pack or audio. A headless client previously received the plugin's sound packet; audible playback of the vanilla `music.game` event on a retail client has not yet been verified.

The old `libRegRsp` resource-pack integration is no longer needed. The plugin does not request a custom pack.

## Behavior and limitations

- A join queues the first `PlaySoundPacket` for the scheduler heartbeat, then repeats it every 4,400 ticks, about 220 seconds at 20 ticks per second.
- Each packet uses the vanilla `music.game` event; the client chooses the built-in music for its game version and settings.
- The packet uses the player's current position when each sound is sent.
- Rejoining replaces the previous task for that player. Quit, offline detection, and plugin disable cancel scheduled playback.
- There are no commands or permissions in `plugin.yml`.

The event name is present in Mojang's [Bedrock sample music definitions](https://github.com/Mojang/bedrock-samples/blob/main/resource_pack/sounds/music_definitions.json). Packet delivery is covered by the lifecycle fixture; audible output still needs a retail-client check.

## Building and installation

The [build workflow](.github/workflows/build.yml) pins [DevTools](https://github.com/NhanAZ/DevTools), analyzes the source, builds a PHAR, and verifies its contents. The project no longer distributes C418 audio. Keep any test server and world separate from production data.

The local playback lifecycle fixture runs with `php tests/scheduler-lifecycle.php`. It checks task replacement, cancellation, and packet position without a connected player. The [build workflow](.github/workflows/build.yml) also runs it before PHPStan and PHAR validation.

## Audio and license

The root [GPL-3.0 license text](LICENSE) applies to the plugin source. No third-party recording is included in the project artifact.

For project issues, use [GitHub Issues](https://github.com/NhanAZ-Plugins/BackgroundMusic/issues). For community support, visit [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
