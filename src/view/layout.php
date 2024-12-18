<!DOCTYPE html>
<html>
  <head>
    <title>Meine MVC-Webanwendung</title>
    <link rel="stylesheet" href="assets/css/design.css">
  </head>
  <body>
  <div style="display: flex; flex-direction: row; flex-grow: 2">
    <?=$nav?>
    <main style="flex-grow: 2; background-color: aqua">
      <?=$content?>
    </main>
  </div>
    <?=$footer?>
  </body>
</html>