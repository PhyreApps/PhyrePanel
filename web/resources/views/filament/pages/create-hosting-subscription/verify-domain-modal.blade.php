<div>

    <h2 class="text-2xl font-semibold text-gray-900 mb-4">
        Your domain must be verified before you can proceed.
    </h2>

    <p class="mb-4">To link your domain to our server, follow these steps:</p>
    <ol class="list-decimal pl-6 mb-4">
        <li>Log in to your domain registrar's <strong>DNS Management Panel</strong>.</li>
        <li>Locate the option to manage <strong>DNS Records</strong> or <strong>Zone Editor</strong>.</li>
        <li>Add a new <strong>A Record</strong> with the following details:</li>
    </ol>

    <div class="overflow-x-auto bg-white dark:bg-white/5 shadow-md rounded-lg">
        <table class="min-w-full text-left table-auto">
            <thead>
            <tr class="bg-primary-500 dark:bg-white/5 text-white">
                <th class="py-2 px-4">Record Type</th>
                <th class="py-2 px-4">Host/Name</th>
                <th class="py-2 px-4">Value (IP Address)</th>
                <th class="py-2 px-4">TTL</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b">
                <td class="py-2 px-4">A</td>
                <td class="py-2 px-4">@ (or {{$domain}})</td>
                <td class="py-2 px-4"><strong>{{$serverIp}}</strong></td>
                <td class="py-2 px-4">300 (or default)</td>
            </tr>
            <tr class="border-b">
                <td class="py-2 px-4">A</td>
                <td class="py-2 px-4">www</td>
                <td class="py-2 px-4"><strong>{{$serverIp}}</strong></td>
                <td class="py-2 px-4">300 (or default)</td>
            </tr>
            </tbody>
        </table>
    </div>

    <p class="mt-4"><strong>4.</strong> Save the changes and wait for DNS propagation (may take a few minutes to several hours).</p>
    <p class="mt-2"><strong>5.</strong> Verify by checking your domain with this command:</p>

    <pre class="bg-gray-200 dark:bg-white/5 p-2 rounded-md my-2"><code>nslookup {{$domain}}</code></pre>
    <p>or</p>
    <pre class="bg-gray-200 dark:bg-white/5 p-2 rounded-md my-2"><code>dig A {{$domain}} +short</code></pre>

    <p class="mt-4"><strong>🔹 Note:</strong> If you want to set up a subdomain (e.g., <code>app.{{$domain}}</code>), use <code>app</code> in the <strong>Host/Name</strong> field instead of <code>@</code>.</p>

</div>
