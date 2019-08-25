<?php 
    $quantidade = $_GET['qtdMultipla'];
?>

<div class="col s12">
    <p class="justificar title prove-text text-vermelho">Selecione a(s) alternativa(s) verdadeira(s)</p>
</div>    
<input type="hidden" name="qtdAltMultipla" value="<?php echo $quantidade ?>">

<?php

    for ($i = 0; $i < $quantidade; $i++) {

?>
    <div class="col s2" style="margin-top: 1.75rem;">
        <label>
            <input type="checkbox" name="alternativaMultipla-correta[]" value="<?php echo $i + 1?>"/>
            <span></span>
        </label>
    </div>

    <div class="col s10">    
        <div class="input-field">
            <input name="alternativaMultipla-<?php echo $i + 1 ?>" id="alternativaMultipla-<?php echo $i + 1 ?>" type="text" class="validate">
            <label for="alternativaMultipla">Alternativa <?php echo $i + 1 ?></label>
        </div>
    </div>

<?php } ?>