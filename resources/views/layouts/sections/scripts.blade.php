<!-- BEGIN: Vendor JS-->

@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/node-waves/node-waves.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/js/menu.js'
])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
  window.OneSignalDeferred = window.OneSignalDeferred || [];
  OneSignalDeferred.push(async function(OneSignal) {
    await OneSignal.init({
      appId: "20e9ecf8-e695-4e4a-80db-277da4b56cea",
      safari_web_id: "web.onesignal.auto.548f76b5-1495-4e31-bc8e-b1b76d8ec8fd",
      notifyButton: {
        enable: true,
      },
    });
  });
</script>
<!-- PushAlert -->
<!-- PushAlert Onsite Messaging -->
<script type="text/javascript">
    (function(d, t) {
        var g = d.createElement(t),
        s = d.getElementsByTagName(t)[0];
        g.src = "https://cdn.inwebr.com/inwebr_19792c764e233b96de78dca4477c58ce.js";
        s.parentNode.insertBefore(g, s);
    }(document, "script"));
</script>
<!-- End PushAlert Onsite Messaging -->
