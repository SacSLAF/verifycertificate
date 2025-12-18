<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

switch ($msg) {
  case '2':
    $message = 'Certificate added successfully.';
    break;
  case '1':
    $message = 'Certificate was not created.';
    break;
  case '3':
    $message = 'Certificate was updated successfully.';
    break;
  case '5':
    $message = 'Certificate was deleted successfully.';
    break;
  case '4':
    $message = 'User added successfully.';
    break;
  case '6':
    $message = 'User was not created.';
    break;
  case '7':
    $message = 'User was updated successfully.';
    break;
  case '8':
    $message = 'User was deleted successfully.';
    break;
  default:
    $message = '';
    break;
}
?>

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>QR Certification Admin Dashboard</title>
  <meta
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    name="viewport" />
  <link
    rel="icon"
    href="assets/img/kaiadmin/favicon.ico"
    type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["assets/css/fonts.min.css"],
      },
      active: function() {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/plugins.min.css" />
  <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link rel="stylesheet" href="assets/css/demo.css" />
</head>