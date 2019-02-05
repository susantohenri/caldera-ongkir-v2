<?php

  if (isset ($_POST['caldera-ongkir-origin'])) update_option('caldera-ongkir-origin', $_POST['caldera-ongkir-origin']);
  $subdistrict_id = get_option('caldera-ongkir-origin');
  $option = getCompleteAddressBySubdistrict($subdistrict_id);
?>
<div class="wrap">
  <H1>Caldera Ongkir Origin Settings</H1>
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

      <!-- origin begin -->
      <tr>
        <th scope="row">
          <label for="caldera-ongkir-origin">RajaOngkir origin</label>
        </th>
        <td>
          <select name="caldera-ongkir-origin">
            <?php if (isset ($option->id)): ?>
              <option value="<?= $option->id ?>"><?= $option->text ?></option>
            <?php endif ?>
          </select>
          <!-- <input name="caldera-ongkir-origin" type="text" class="regular-text" value="<?= get_option('caldera-ongkir-origin') ?>"> -->
        </td>
      </tr>
      <!-- origin end -->

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
<link rel="stylesheet" type="text/css" href="<?= CALDERA_ONGKIR_URL . 'select2-full.min.css' ?>">
<script type="text/javascript" src="<?= CALDERA_ONGKIR_URL . 'select2-full.min.js' ?>"></script>
<script type="text/javascript">
  jQuery(function () {
    jQuery('[name="caldera-ongkir-origin"]').select2({
      width: '500px',
      ajax: {
        url: '<?= site_url('wp-json/caldera-ongkir/address') ?>',
        type: 'POST', dataType: 'json'
      }
    })
  })
</script>