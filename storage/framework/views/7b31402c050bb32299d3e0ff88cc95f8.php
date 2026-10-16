
<div id="VerixaSplashScreen" class="Verixa-splash-overlay" onclick="dismissSplash()">
    <!-- Ambient Radial Backdrop -->
    <div class="splash-ambient-glow"></div>
    <div class="splash-dot-matrix"></div>

    <!-- Centered Logo Display -->
    <div class="splash-logo-centerpiece">
        <div class="logo-ring-wrapper">
            <div class="logo-ring ring-3"></div>
            <div class="logo-ring ring-2"></div>
            <div class="logo-ring ring-1"></div>
            <div class="logo-badge-container">
                <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['size' => '110','glow' => true,'badge' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => '110','glow' => true,'badge' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $attributes = $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $component = $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.Verixa-splash-overlay {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    min-height: -webkit-fill-available;
    background: #0A0718;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    overflow: hidden;
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.Verixa-splash-overlay.fade-out {
    opacity: 0;
    transform: scale(1.05);
    pointer-events: none;
}

/* Ambient Backdrop */
.splash-ambient-glow {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background: 
        radial-gradient(circle 600px at 50% 50%, rgba(124, 58, 237, 0.25) 0%, transparent 65%),
        radial-gradient(circle 450px at 50% 50%, rgba(6, 182, 212, 0.15) 0%, transparent 60%),
        radial-gradient(circle at center, #150F30 0%, #0A0718 100%);
}

.splash-dot-matrix {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    opacity: 0.15;
    background-image: radial-gradient(circle, #7C3AED 1.2px, transparent 1.2px);
    background-size: 24px 24px;
}

/* Centerpiece Container */
.splash-logo-centerpiece {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: splashPopIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.logo-ring-wrapper {
    position: relative;
    width: 170px;
    height: 170px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-ring {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.ring-1 {
    width: 130px;
    height: 130px;
    border: 1.5px stroke rgba(124, 58, 237, 0.5);
    background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
    box-shadow: 0 0 35px rgba(124, 58, 237, 0.45);
    animation: splashRingPulse 2.2s ease-in-out infinite alternate;
}

.ring-2 {
    width: 154px;
    height: 154px;
    border: 1px dashed rgba(6, 182, 212, 0.45);
    animation: splashRingRotate 20s linear infinite;
}

.ring-3 {
    width: 170px;
    height: 170px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.logo-badge-container {
    position: relative;
    z-index: 3;
    animation: splashFloatLogo 3.2s ease-in-out infinite alternate;
}

@keyframes splashPopIn {
    0% { transform: scale(0.85); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes splashRingPulse {
    0% { transform: scale(0.96); opacity: 0.5; }
    100% { transform: scale(1.08); opacity: 0.95; }
}

@keyframes splashRingRotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes splashFloatLogo {
    0% { transform: translateY(-4px); }
    100% { transform: translateY(4px); }
}
</style>

<script>
(function() {
    var splash = document.getElementById('VerixaSplashScreen');
    if (!splash) return;

    // Fast-dismiss check if already seen in current session
    try {
        if (sessionStorage.getItem('Verixa_native_splash_seen')) {
            splash.style.display = 'none';
            if (splash.parentNode) splash.parentNode.removeChild(splash);
            return;
        }
        sessionStorage.setItem('Verixa_native_splash_seen', '1');
    } catch(e) {}

    var dismissed = false;

    var timer = setTimeout(function() {
        dismissSplash();
    }, 1400);

    window.dismissSplash = function() {
        if (dismissed) return;
        dismissed = true;
        clearTimeout(timer);
        splash.classList.add('fade-out');
        setTimeout(function() {
            if (splash && splash.parentNode) {
                splash.parentNode.removeChild(splash);
            }
        }, 400);
    };
})();
</script>
<?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/components/splashscreen.blade.php ENDPATH**/ ?>