<x-layouts::app :title="__('Site Settings')">
    <div class="mx-auto max-w-5xl space-y-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ __('Site Settings') }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Update your branding and SEO information for the public storefront.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @csrf

            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Branding') }}</h2>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label for="site_title" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Site title') }}</label>
                        <input id="site_title" name="site_title" type="text" value="{{ old('site_title', $settings->site_title ?? config('app.name', 'Creativ Stock')) }}" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>

                    <div class="space-y-2">
                        <label for="meta_description" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Meta description') }}</label>
                        <textarea id="meta_description" name="meta_description" rows="4" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-3">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Logo') }}</label>
                        <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-zinc-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#0fa3ad] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-zinc-300" />
                        </div>
                        @if (! empty($settings->logo_path))
                            <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Current logo" class="mt-3 h-16 w-auto rounded-lg border border-zinc-200 bg-white p-2 dark:border-zinc-700" />
                        @endif
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Favicon') }}</label>
                        <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                            <input type="file" name="favicon" accept=".ico,image/*" class="block w-full text-sm text-zinc-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#0fa3ad] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-zinc-300" />
                        </div>
                        @if (! empty($settings->favicon_path))
                            <img src="{{ asset('storage/'.$settings->favicon_path) }}" alt="Current favicon" class="mt-3 h-10 w-10 rounded-md border border-zinc-200 bg-white p-1 dark:border-zinc-700" />
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Payment Gateway') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Set the live Paddle credentials for production mode. Leave any field blank to keep the current value.') }}</p>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label for="paddle_environment" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Environment') }}</label>
                        <select id="paddle_environment" name="paddle_environment" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="sandbox" {{ old('paddle_environment', $settings->paddle_environment ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>{{ __('Sandbox') }}</option>
                            <option value="live" {{ old('paddle_environment', $settings->paddle_environment ?? 'sandbox') === 'live' || old('paddle_environment', $settings->paddle_environment ?? 'sandbox') === 'production' ? 'selected' : '' }}>{{ __('Live') }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="paddle_verify_ssl" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Verify SSL') }}</label>
                        <select id="paddle_verify_ssl" name="paddle_verify_ssl" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="1" {{ old('paddle_verify_ssl', (string) ($settings->paddle_verify_ssl ?? true)) === '1' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                            <option value="0" {{ old('paddle_verify_ssl', (string) ($settings->paddle_verify_ssl ?? true)) === '0' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label for="paddle_api_key" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('API key') }}</label>
                        <input id="paddle_api_key" name="paddle_api_key" type="password" value="" placeholder="{{ __('Leave blank to keep the current key') }}" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>

                    <div class="space-y-2">
                        <label for="paddle_client_token" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Client token') }}</label>
                        <input id="paddle_client_token" name="paddle_client_token" type="password" value="" placeholder="{{ __('Leave blank to keep the current token') }}" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label for="paddle_webhook_secret" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Webhook secret') }}</label>
                        <input id="paddle_webhook_secret" name="paddle_webhook_secret" type="password" value="" placeholder="{{ __('Leave blank to keep the current secret') }}" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>

                    <div class="space-y-2">
                        <label for="paddle_test_price_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Price ID / test product ID') }}</label>
                        <input id="paddle_test_price_id" name="paddle_test_price_id" type="text" value="{{ old('paddle_test_price_id', $settings->paddle_test_price_id ?? '') }}" placeholder="{{ __('Optional') }}" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-[#0fa3ad] focus:ring-2 focus:ring-[#0fa3ad]/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">{{ __('Back to dashboard') }}</a>
                <button type="submit" class="rounded-lg bg-[#0fa3ad] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#0c8f95]">{{ __('Save settings') }}</button>
            </div>
        </form>
    </div>
</x-layouts::app>
