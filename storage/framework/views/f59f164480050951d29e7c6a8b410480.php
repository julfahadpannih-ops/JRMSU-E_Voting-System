<?php $__env->startSection('page-title', 'Live Election Results'); ?>
<?php $__env->startSection('page-subtitle', 'Real-time vote tabulation.'); ?>
<?php $activeView = 'results'; ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <p class="text-xs text-gray-400">Auto-refreshes every 10 seconds.</p>
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.results.export.csv')); ?>"
           class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow flex items-center gap-1">
            📥 Export CSV
        </a>
        <a href="<?php echo e(route('admin.results.export.pdf')); ?>" target="_blank"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow flex items-center gap-1">
            🖨️ Print / PDF
        </a>
        <button onclick="confirmReset()"
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow">
            🗑 Reset All Votes
        </button>
    </div>
</div>

<div id="results-container" class="space-y-6">
    <div class="text-center text-gray-400 py-12">Loading results…</div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function loadResults() {
    try {
        const response = await fetch('<?php echo e(route("admin.results.index")); ?>', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const res = await response.json();
        if (!res.success) return;

        const container = document.getElementById('results-container');

        if (!res.data || res.data.length === 0) {
            container.innerHTML = '<div class="bg-white rounded-2xl shadow p-8 text-center text-gray-400">No votes recorded yet.</div>';
            return;
        }

        const grouped = {};
        res.data.forEach(r => {
            if (!grouped[r.positionId]) {
                grouped[r.positionId] = { positionName: r.positionName, maxVotes: r.maxVotes, candidates: [] };
            }
            grouped[r.positionId].candidates.push(r);
        });

        container.innerHTML = '';
        Object.values(grouped).forEach(group => {
            const { positionName, maxVotes, candidates } = group;
            const sorted = [...candidates].sort((a, b) => b.voteCount - a.voteCount);
            const topCount = sorted[0]?.voteCount ?? 0;

            const rows = sorted.map((c, i) => {
                const isLeader = c.voteCount > 0 && c.voteCount === topCount;
                const avatar   = c.image
                    ? `<img src="${c.image}" class="h-8 w-8 rounded-full object-cover mr-3 inline-block border border-gray-200">`
                    : `<div class="h-8 w-8 rounded-full bg-gray-200 inline-flex items-center justify-center text-gray-600 font-bold mr-3 text-xs">${c.candidateName.charAt(0)}</div>`;
                return `
                <tr class="${isLeader ? 'bg-yellow-50' : i % 2 === 0 ? 'bg-white' : 'bg-gray-50'} hover:bg-blue-50 transition-colors">
                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-500">${i + 1}</td>
                    <td class="px-4 py-3 flex items-center text-sm">${avatar}<span class="font-medium text-primary">${c.candidateName}</span></td>
                    <td class="px-4 py-3 text-sm text-gray-500">${c.partyList || 'Independent'}</td>
                    <td class="px-4 py-3 text-center text-2xl font-extrabold text-primary">${c.voteCount}</td>
                    <td class="px-4 py-3 text-center">${isLeader ? '<span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">LEADER</span>' : ''}</td>
                </tr>`;
            }).join('');

            container.innerHTML += `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-primary text-lg">${positionName}</h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Max ${maxVotes} winner${maxVotes > 1 ? 's' : ''}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 text-xs font-bold text-primary uppercase tracking-widest">
                                <th class="px-4 py-3 text-center w-12">Rank</th>
                                <th class="px-4 py-3 text-left">Candidate</th>
                                <th class="px-4 py-3 text-left">Party</th>
                                <th class="px-4 py-3 text-center w-24">Votes</th>
                                <th class="px-4 py-3 text-center w-28">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">${rows}</tbody>
                    </table>
                </div>
            </div>`;
        });
    } catch (e) {
        console.error('Failed to load results:', e);
    }
}

async function confirmReset() {
    if (!confirm('⚠️ RESET ALL VOTES? This cannot be undone!')) return;
    try {
        const response = await fetch('<?php echo e(route("admin.results.reset")); ?>', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        const res = await response.json();
        alert(res.success ? res.message : 'Error: ' + res.message);
        if (res.success) loadResults();
    } catch (e) {
        alert('Failed to reset votes.');
    }
}

loadResults();
setInterval(loadResults, 10000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JRMSU E_VOTING SYSTEM\resources\views/admin/results.blade.php ENDPATH**/ ?>