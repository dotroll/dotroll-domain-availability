<?php
/**
 * result, Result template for Domain Search
 * 
 * @copyright Copyright (c) 2007 DotRoll Kft. (http://www.dotroll.com)
 * @author Zoltán Istvanovszki <zoltan.istvanovszki@dotroll.com>
 * @since 2016.09.20.
 * @package dotroll-domain-availability
 * @license https://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3
 */
?>
<?= isset($result) && $result === false ? "<h2><span class='label label-danger'>A beírt domain '{$_POST['domain']}' nem érvényes</span></h2>" : '' ?>
<?= isset($result[0]['available']) && $result[0]['available'] === true ? "<h2><span class='label label-success'>Gratulálunk! {$result[0]['domain']} regisztrálható!</span></h2>" : '' ?>
<?= isset($result[0]['available']) && $result[0]['available'] === false ? "<h2><span class='label label-danger'>Sajnáljuk! {$result[0]['domain']} foglalt!</span></h2>" : '' ?>
<?php if (isset($result) && \is_array($result)): ?>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Domain név</th>
				<th>Állapot</th>
				<th>Tovább</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($result as $row): ?>
				<tr class="<?= $row['available'] === null ? 'danger' : ($row['available'] === true ? 'success' : 'info') ?>">
					<td><?= $row['domain'] ?></td>
					<td><?= $row['available'] === null ? 'Nem tudtunk a regisztrátorhoz csatlakozni. Kérjük, próbálja meg később.' : ($row['available'] === true ? 'Elérhető, regisztrálja most!' : 'Foglalt') ?></td>
					<td><?php if ($row['available'] !== null): ?><a class="btn btn-<?= $row['available'] === true ? 'success' : 'warning' ?>" href="<?= $row['link'] ?>" target="_blank"><?= $row['available'] === true ? 'Regisztráció' : 'Átregisztráció' ?></a><?php endif; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
