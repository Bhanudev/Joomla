<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach ($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<?php echo $item->TemplateName; ?>
		</td>
		<td>
			<?php echo $item->Subject; ?>
		</td>
		<td>
			<?php echo $item->SenderName; ?>
		</td>
		<td>
			<?php echo $item->SenderEmail; ?>
		</td>
		<td>
			<?php echo $item->Body; ?>
		</td>
	</tr>
<?php endforeach; ?>
