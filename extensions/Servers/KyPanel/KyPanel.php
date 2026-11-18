<?php

namespace Paymenter\Extensions\Servers\KyPanel;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use App\Rules\Domain;
use Exception;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'KyPanel',
    description: 'Sell and manage hosting accounts on KyPanel servers.',
    version: '1.0.0',
    author: 'KyPanel',
    url: 'https://github.com/KyPanel/paymenter-module',
    icon: 'data:image/png;base64,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',
)]
class KyPanel extends Server
{
    private array $ids = [];

    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'type' => 'text',
                'label' => 'Hostname',
                'placeholder' => 'server.example.com',
                'required' => true,
            ],
            [
                'name' => 'port',
                'type' => 'number',
                'label' => 'Port',
                'default' => 7711,
                'required' => false,
            ],
            [
                'name' => 'apikey',
                'type' => 'text',
                'label' => 'API token',
                'description' => 'Ky Admin › Development › Manage API Tokens, with "accounts" (read and change) and "packages" (read).',
                'required' => true,
                'encrypted' => true,
            ],
            [
                'name' => 'verify_ssl',
                'type' => 'checkbox',
                'label' => 'Verify the SSL certificate (untick only for a new server with a self-signed one)',
                'default' => true,
                'required' => false,
            ],
        ];
    }

    public function getProductConfig($values = []): array
    {
        $options = [];
        foreach ($this->request('GET', '/api/v1/packages')['items'] ?? [] as $p) {
            $options[] = ['value' => $p['name'], 'label' => $p['name']];
        }

        return [
            [
                'name' => 'package',
                'type' => 'select',
                'label' => 'KyPanel package',
                'options' => $options,
                'required' => true,
            ],
            [
                'name' => 'reseller',
                'type' => 'checkbox',
                'label' => 'Create the account as a reseller',
                'required' => false,
            ],
        ];
    }

    public function getCheckoutConfig()
    {
        return [
            [
                'name' => 'domain',
                'type' => 'text',
                'label' => 'Domain',
                'required' => true,
                'validation' => [new Domain, 'required'],
                'placeholder' => 'example.com',
            ],
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->request('GET', '/api/v1/packages');
            $this->request('GET', '/api/v1/accounts', null, ['page' => 1, 'per_page' => 1]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function createServer(Service $service, $settings, $properties)
    {
        $domain = strtolower(preg_replace('/^\s*www\./i', '', trim((string) $properties['domain'])));
        $username = $this->freeUsername($domain);

        $body = [
            'username' => $username,
            'domain' => $domain,
            'password' => $this->password(),
            'contact_email' => (string) $service->user->email,
            'package_id' => $this->packageId($properties['package'] ?? $settings['package'] ?? ''),
        ];
        if (!empty($settings['reseller'])) {
            $body['make_reseller'] = true;
        }
        $res = $this->request('POST', '/api/v1/accounts', $body);
        $this->wait((string) ($res['job_id'] ?? ''));

        $service->properties()->updateOrCreate(
            ['key' => 'kypanel_username'],
            ['name' => 'KyPanel username', 'value' => $username]
        );

        return true;
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        $res = $this->request('POST', $this->accountPath($properties) . '/suspend', ['reason' => 'Suspended by billing']);
        $this->wait((string) ($res['job_id'] ?? ''));

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        $res = $this->request('POST', $this->accountPath($properties) . '/unsuspend', (object) []);
        $this->wait((string) ($res['job_id'] ?? ''));

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        $username = $this->username($properties);
        $res = $this->request('DELETE', $this->accountPath($properties), null, ['confirm' => $username]);
        $this->wait((string) ($res['job_id'] ?? ''));
        $service->properties()->where('key', 'kypanel_username')->delete();

        return true;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        $res = $this->request('PUT', $this->accountPath($properties) . '/package', [
            'package_id' => $this->packageId($properties['package'] ?? $settings['package'] ?? ''),
        ]);
        $this->wait((string) ($res['job_id'] ?? ''));

        return true;
    }

    public function getActions(Service $service, $settings = [], $properties = []): array
    {
        if (!$service->properties()->where('key', 'kypanel_username')->exists()) {
            return [];
        }

        return [
            [
                'type' => 'button',
                'label' => 'Log in to KyPanel',
                'function' => 'ssoLink',
            ],
        ];
    }

    // one-time sign-in link, valid for 60 seconds
    public function ssoLink(Service $service, $settings = [], $properties = []): string
    {
        $username = (string) $service->properties()->where('key', 'kypanel_username')->value('value');
        $panel = !empty($settings['reseller']) ? 'admin' : 'user';
        $res = $this->request('POST', '/api/v1/accounts/' . rawurlencode($this->accountId($username)) . '/sso', ['panel' => $panel]);
        if (empty($res['url'])) {
            throw new Exception('Unable to generate the KyPanel login link');
        }

        return $res['url'];
    }

    private function username($properties): string
    {
        $username = $properties['kypanel_username'] ?? '';
        if ($username === '') {
            throw new Exception('Service has not been created');
        }

        return strtolower($username);
    }

    private function accountPath($properties): string
    {
        return '/api/v1/accounts/' . rawurlencode($this->accountId($this->username($properties)));
    }

    private function accountId(string $username): string
    {
        $username = strtolower(trim($username));
        if (isset($this->ids[$username])) {
            return $this->ids[$username];
        }
        $page = $this->request('GET', '/api/v1/accounts', null, ['q' => $username, 'page' => 1, 'per_page' => 100]);
        foreach ($page['items'] ?? [] as $a) {
            if (strtolower($a['username'] ?? '') === $username) {
                return $this->ids[$username] = $a['id'];
            }
        }
        throw new Exception('There is no account named "' . $username . '" on this KyPanel server.');
    }

    // accepts the package name or id
    private function packageId(string $nameOrId): string
    {
        $want = trim($nameOrId);
        $names = [];
        foreach ($this->request('GET', '/api/v1/packages')['items'] ?? [] as $p) {
            if ($p['id'] === $want || strcasecmp($p['name'], $want) === 0) {
                return $p['id'];
            }
            $names[] = $p['name'];
        }
        throw new Exception('There is no package "' . $want . '" on this KyPanel server. Packages: ' . ($names ? implode(', ', $names) : 'none yet') . '.');
    }

    // KyPanel usernames: a letter, then letters or digits, 16 max
    private function freeUsername(string $domain): string
    {
        $base = ltrim(preg_replace('/[^a-z0-9]/', '', $domain), '0123456789');
        $base = substr($base !== '' ? $base : 'user', 0, 8);
        $candidates = [$base];
        for ($i = 1; $i < 1000; $i++) {
            $candidates[] = substr($base, 0, 8 - strlen((string) $i)) . $i;
        }
        foreach ($candidates as $name) {
            try {
                $this->accountId($name);
            } catch (Exception $e) {
                return $name;
            }
        }
        throw new Exception('Couldn\'t find a free username for ' . $domain);
    }

    private function password(int $length = 16): string
    {
        $pool = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $pool[random_int(0, strlen($pool) - 1)];
        }

        return $out;
    }

    // create/suspend/terminate run as jobs on the server, poll until done
    private function wait(string $jobId, int $seconds = 240): void
    {
        if ($jobId === '') {
            return;
        }
        @set_time_limit($seconds + 60);
        $until = time() + $seconds;
        $delay = 1;
        do {
            $job = $this->request('GET', '/api/v1/jobs/' . rawurlencode($jobId))['job'] ?? [];
            $status = $job['status'] ?? '';
            if ($status === 'succeeded') {
                return;
            }
            if (in_array($status, ['failed', 'canceled', 'cancelled'], true)) {
                throw new Exception($job['error'] ?? 'The KyPanel job failed.');
            }
            sleep($delay);
            $delay = min($delay + 1, 5);
        } while (time() < $until);
        throw new Exception('KyPanel is still working on it (job ' . $jobId . '). Check Ky Admin before trying again.');
    }

    private function request(string $method, string $path, $body = null, array $query = []): array
    {
        $port = (int) ($this->config('port') ?: 7711);
        $url = 'https://' . trim((string) $this->config('host'), "/ \t") . ':' . $port . $path;
        $http = Http::withToken((string) $this->config('apikey'))
            ->acceptJson()
            ->withUserAgent('KyPanel-Paymenter')
            ->timeout(60);
        if (!$this->config('verify_ssl')) {
            $http = $http->withoutVerifying();
        }
        if ($query) {
            $http = $http->withQueryParameters($query);
        }
        $response = $body === null ? $http->send($method, $url) : $http->send($method, $url, ['json' => $body]);

        if ($response->successful()) {
            return $response->json() ?? [];
        }
        $p = $response->json() ?? [];
        $msg = $p['title'] ?? 'HTTP ' . $response->status();
        if (!empty($p['detail'])) {
            $msg .= ': ' . $p['detail'];
        }
        if (!empty($p['fields']) && is_array($p['fields'])) {
            $parts = [];
            foreach ($p['fields'] as $field => $text) {
                $parts[] = $field . ': ' . $text;
            }
            $msg .= ' (' . implode('; ', $parts) . ')';
        }
        throw new Exception($msg);
    }
}
