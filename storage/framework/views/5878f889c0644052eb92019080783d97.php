<?php $__env->startSection('body'); ?>
<div class="flex min-h-screen">
    <?php if (isset($component)) { $__componentOriginal6fc2d165f80d597f34aa0f8014c366d2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6fc2d165f80d597f34aa0f8014c366d2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-sidebar','data' => ['active' => $activeView ?? '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeView ?? '')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6fc2d165f80d597f34aa0f8014c366d2)): ?>
<?php $attributes = $__attributesOriginal6fc2d165f80d597f34aa0f8014c366d2; ?>
<?php unset($__attributesOriginal6fc2d165f80d597f34aa0f8014c366d2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6fc2d165f80d597f34aa0f8014c366d2)): ?>
<?php $component = $__componentOriginal6fc2d165f80d597f34aa0f8014c366d2; ?>
<?php unset($__componentOriginal6fc2d165f80d597f34aa0f8014c366d2); ?>
<?php endif; ?>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <button id="open-sidebar" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-primary"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                    <p class="text-xs text-gray-400 hidden sm:block"><?php echo $__env->yieldContent('page-subtitle', ''); ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="hidden sm:block text-sm text-gray-600">
                    Welcome, <strong><?php echo e(Auth::guard('admin')->user()->name); ?></strong>
                </span>
                <div class="h-9 w-9 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
                    <?php echo e(strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1))); ?>

                </div>
            </div>
        </header>

        
        <?php if(session('success')): ?>
        <div class="mx-4 md:mx-8 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg" id="flash-msg">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <div class="mx-4 md:mx-8 mt-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg" id="flash-msg">
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <main class="flex-1 p-4 md:p-8 overflow-auto">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Sidebar mobile toggle
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    document.getElementById('open-sidebar')?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });
    document.getElementById('close-sidebar')?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
    // Auto-hide flash
    setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);

    // Global CSRF helper for fetch()
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    window.apiFetch = async (url, options = {}) => {
        const defaults = {
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        };
        const merged = { ...defaults, ...options, headers: { ...defaults.headers, ...options.headers } };
        const res = await fetch(url, merged);
        return res.json();
    };
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JRMSU E_VOTING SYSTEM\resources\views/layouts/admin.blade.php ENDPATH**/ ?>