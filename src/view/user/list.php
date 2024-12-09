<table>
  <thead>
  <tr>
    <th>ID</th>
    <th>Name</th>
  </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user) : ?>
    <tr>
      <td><?=$user['id']?></td>
      <td><?=$user['name']?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>