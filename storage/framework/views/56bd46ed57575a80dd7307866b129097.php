<?php $__env->startSection('page-title', 'Positions Management'); ?>
<?php $__env->startSection('page-subtitle', 'Define the electoral positions for this election.'); ?>
<?php $activeView = 'positions'; ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-primary">Electoral Positions</h2>
        <button onclick="openModal()"
            class="bg-primary hover:bg-secondary text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow">
            + Add Position
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">#</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Position Name</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Max Votes</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-widest text-primary bg-slate-50">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors" id="pos-row-<?php echo e($position->id); ?>">
                    <td class="px-6 py-4 text-xs text-gray-400"><?php echo e($position->id); ?></td>
                    <td class="px-6 py-4 font-medium text-sm text-primary"><?php echo e($position->name); ?></td>
                    <td class="px-6 py-4 text-center text-sm"><?php echo e($position->max_votes); ?> vote<?php echo e($position->max_votes > 1 ? 's' : ''); ?></td>
                    <td class="px-6 py-4 text-center space-x-3">
                        <button onclick="openModal(<?php echo e(json_encode(['id'=>$position->id,'name'=>$position->name,'max_votes'=>$position->max_votes])); ?>)"
                            class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Edit</button>
                        <button onclick="deletePosition(<?php echo e($position->id); ?>, '<?php echo e(addslashes($position->name)); ?>')"
                            class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No positions defined yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="pos-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="font-bold text-primary" id="modal-title">Add Position</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="pos-id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Position Name</label>
                <input type="text" id="p-name" class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="e.g. SSG President">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Max Votes Allowed</label>
                <input type="number" id="p-max_votes" min="1" max="10" value="1" class="border border-gray-300 rounded-lg text-sm p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-secondary">
            </div>
            <p id="modal-error" class="text-red-600 text-xs hidden"></p>
        </div>
        <div class="flex justify-end gap-3 p-6 border-t">
            <button onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-xl">Cancel</button>
            <button onclick="savePosition()" class="px-5 py-2 text-sm bg-primary hover:bg-secondary text-white font-semibold rounded-xl transition">Save</button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openModal(data = null) {
    document.getElementById('pos-modal').classList.remove('hidden');
    document.getElementById('modal-title').textContent = data ? 'Edit Position' : 'Add Position';
    document.getElementById('pos-id').value       = data?.id ?? '';
    document.getElementById('p-name').value       = data?.name ?? '';
    document.getElementById('p-max_votes').value  = data?.max_votes ?? 1;
    document.getElementById('modal-error').classList.add('hidden');
}
function closeModal() { document.getElementById('pos-modal').classList.add('hidden'); }

async function savePosition() {
    const id      = document.getElementById('pos-id').value;
    const payload = { name: document.getElementById('p-name').value.trim(), max_votes: document.getElementById('p-max_votes').value };
    const url     = id ? `/admin/positions/${id}` : '<?php echo e(route("admin.positions.store")); ?>';
    const method  = id ? 'PUT' : 'POST';
    const res     = await apiFetch(url, { method, body: JSON.stringify(payload) });
    if (res.success) { closeModal(); location.reload(); }
    else {
        const err = document.getElementById('modal-error');
        err.textContent = res.message || 'Error saving.';
        err.classList.remove('hidden');
    }
}

async function deletePosition(id, name) {
    if (!confirm(`Delete position "${name}"? This will also delete all associated candidates and votes!`)) return;
    const res = await apiFetch(`/admin/positions/${id}`, { method: 'DELETE' });
    if (res.success) document.getElementById(`pos-row-${id}`)?.remove();
    else alert(res.message);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JRMSU E_VOTING SYSTEM\resources\views/admin/positions.blade.php ENDPATH**/ ?>