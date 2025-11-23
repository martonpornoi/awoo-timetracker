<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awoo – TimeTracker</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

    @php
        use Illuminate\Support\Str;

        // Load README and convert Markdown → HTML
        $readmePath = base_path('README.md');
        $readme = '';

        try {
            if (file_exists($readmePath)) {
                $content = file_get_contents($readmePath);
                $readme = Str::markdown($content);
            } else {
                $readme = "<p><em>README.md not found at: {$readmePath}</em></p>";
            }
        } catch (\Throwable $e) {
            $readme = "<p><em>Failed to read README.md: {$e->getMessage()}</em></p>";
        }
    @endphp

    <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">

        {{-- HEADER --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-wide">
                Awoo – TimeTracker
            </h1>
            <p class="text-gray-600 mt-2 text-lg">
                Simple and fast timesheet tracking for your projects.
            </p>
        </div>

        {{-- BUTTONS --}}
        <div class="flex gap-4 mb-10">
            <a href="{{ route('login') }}"
               class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition">
                Register
            </a>
        </div>

        {{-- COLLAPSIBLE README --}}
        <div class="max-w-3xl w-full bg-white rounded-xl shadow-lg overflow-hidden">
            <details class="group">
                <summary
                    class="cursor-pointer select-none px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
                    <span class="text-lg font-semibold">README</span>
                    <span class="text-sm text-gray-500 group-open:hidden">show</span>
                    <span class="text-sm text-gray-500 hidden group-open:inline">hide</span>
                </summary>

                <div class="p-6 md:p-8">
                    <div class="readme-markdown max-w-none overflow-x-auto">
                        {!! $readme !!}
                    </div>
                </div>
            </details>
        </div>

        {{-- FOOTER --}}
        <footer class="mt-10 text-sm text-gray-500">
            <span>
                Version:
                <b class="text-gray-700">{{ env('APP_VERSION', 'dev') }}</b>
            </span>
            <span class="mx-2">•</span>
            <a href="https://github.com/martonpornoi/awoo-timetracker"
               target="_blank"
               class="underline hover:text-gray-700">
                GitHub
            </a>
        </footer>

    </div>

</body>
</html>
