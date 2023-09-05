<?php

namespace Valet;

use GuzzleHttp\Client;
use Composer\CaBundle\CaBundle;

class Valet
{
	protected $cli;
	protected $files;

	/**
	 * Create a new Valet instance.
	 *
	 * @param  CommandLine  $cli
	 * @param  Filesystem  $files
	 * @return void
	 */
	public function __construct(CommandLine $cli, Filesystem $files)
	{
		$this->cli = $cli;
		$this->files = $files;
	}

	/**
	 * Get the paths to all of the Valet extensions.
	 *
	 * @return array
	 */
	public function extensions(): array
	{
		$path = static::homePath('Extensions');

		if (!$this->files->isDir($path)) {
			return [];
		}

		return collect($this->files->scandir($path))
			->reject(function ($file) {
				return is_dir($file);
			})
			->map(function ($file) use ($path) {
				return "$path/$file";
			})
			->values()->all();
	}

	/**
	 * Get the installed Valet services.
	 *
	 * @param bool $disable Don't show the progressbar.
	 * Used in `install` command to query if the services are running.
	 *
	 * @return array
	 */
	public function services($disable = false): array
	{
		$phps = \Configuration::get('php', []);

		$phpCGIs = collect([]);
		$phpXdebugCGIs = collect([]);
		foreach ($phps as $php) {
			$phpCGIs->put("php {$php['version']}", \PhpCgi::getPhpCgiName($php['version']));
			$phpXdebugCGIs->put("php-xdebug {$php['version']}", \PhpCgiXdebug::getPhpCgiName($php['version']));
		}

		$services = collect([
			'acrylic' => 'AcrylicDNSProxySvc',
			'nginx' => 'valet_nginx',
		])->merge($phpCGIs)->merge($phpXdebugCGIs);

		// Set empty variable first, prevents errors when $disable is true.
		$progressBar = "";

		if (!$disable) {
			$progressBar = progressbar($services->count(), "Checking");
		}

		return $services->map(function ($id, $service) use ($progressBar, $disable) {
			$output = $this->cli->run('powershell -command "Get-Service -Name ' . $id . '"');

			if (!$disable) {
				$progressBar->setMessage(ucfirst($service), "placeholder");
				$progressBar->advance();
			}

			if (strpos($output, 'Running') > -1) {
				$status = '<fg=green>running</>';
			} elseif (strpos($output, 'Stopped') > -1) {
				$status = '<fg=yellow>stopped</>';
			} else {
				$status = '<fg=red>missing</>';
			}

			if (strpos($status, "missing") && strpos($service, "xdebug")) {
				$status = '<fg=red>not installed</>';
			}

			return [
				'service' => $service,
				'winname' => $id,
				'status' => $status,
			];
		})->values()->all();
	}

	/**
	 * Determine if this is the latest version of Valet.
	 *
	 * @param  string  $currentVersion
	 * @return bool
	 *
	 * @throws \GuzzleHttp\Exception
	 */
	public function onLatestVersion($currentVersion): bool
	{
		/**
		 * Set a new GuzzleHttp client and use the Composer\CaBundle package
		 * to find and use the TLS CA bundle in order to verify the TLS/SSL
		 * certificate of the requesting website/API.
		 * Otherwise, Guzzle errors out with a curl error.
		 *
		 * Code from StackOverflow answer: https://stackoverflow.com/a/53823135/2358222
		 */
		$client = new Client([
			\GuzzleHttp\RequestOptions::VERIFY => CaBundle::getSystemCaRootBundlePath()
		]);

		// Create a GuzzleHttp get request to the ngrok tunnels API.
		$get = $client->request(
			"GET",
			'https://api.github.com/repos/ycodetech/valet-windows/releases/latest'
		);
		$response = json_decode($get->getBody()->getContents());

		return version_compare($currentVersion, trim($response->tag_name, 'v'), '>=');
	}

	/**
	 * Run composer global diagnose.
	 */
	public function composerGlobalDiagnose()
	{
		$this->cli->runAsUser('composer global diagnose');
	}

	/**
	 * Run composer global update.
	 */
	public function composerGlobalUpdate()
	{
		$this->cli->runAsUser('composer global update');
	}

	/**
	 * Get the path to the home directory of composer global.
	 *
	 * While the default is "~/AppData/Roaming/Composer",
	 * composer does allow the user to change where global packages are installed.
	 * So we need to essentially ask composer where the home directory is.
	 *
	 * This is used to set the `COMPOSER_GLOBAL_PATH` constant, which in turn is used
	 * by the `Diagnose` class.
	 *
	 * @return string The path to the global composer directory.
	 */
	public function getComposerGlobalPath()
	{
		return $this->cli->runAsUser('composer -n config --global home');
	}

	/**
	 * Get the Valet home path (VALET_HOME_PATH = ~/.config/valet).
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function homePath(string $path = ''): string
	{
		return VALET_HOME_PATH . ($path ? "/$path" : $path);
	}
}
