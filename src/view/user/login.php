<main>
  <form action="?c=User&a=login" method="post" class="loginWrapper">
    <?=$errors?>
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>
  </form>
</main>