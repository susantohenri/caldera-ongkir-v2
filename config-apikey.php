<?php if (isset ($_POST['caldera-ongkir-apikey'])) update_option('caldera-ongkir-apikey', $_POST['caldera-ongkir-apikey']); ?>
<div class="wrap">
  <H1>Caldera Ongkir API Key Settings</H1>
  <form method="POST">
  <table class="form-table">

    <?php if ($_POST): ?>
    <caption>
      <div class="notice notice-success is-dismissible">
        <p>Changes Saved</p>
      </div>
    </caption>
    <?php endif ?>

    <tbody>

      <!-- apikey begin -->
      <tr>
        <th scope="row">
          <label for="caldera-ongkir-apikey">RajaOngkir APIKEY</label>
        </th>
        <td>
          <input name="caldera-ongkir-apikey" type="text" class="regular-text" value="<?= get_option('caldera-ongkir-apikey') ?>">
        </td>
      </tr>
      <!-- apikey end -->

      <!-- submit begin -->
      <tr>
        <th scope="row"></th>
        <td>
          <input type="submit" name="caldera-ongkir-submit" class="button button-primary" value="Save Changes">
        </td>
      </tr>
      <!-- submit end -->

    </tbody>

  </table>
  </form>
</div>