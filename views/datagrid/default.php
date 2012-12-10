<?php
 //Default View for any Datagrid:
 //Use this File as a template to modify your view:

//Determine if we have multiple Objects or just one or zero:
if(is_object($this->data['GridData'])){
    //we have just one, so we use a workaround here:
    $this->data['GridData'] = array(0=>$this->data['GridData']);
}elseif($this->data['GridData'] == false){
    //We have none, however we create an empty array:
    $this->data['GridData'] = array();
}
?>
<!--  TODO generate id und condition based classes -->
<table class="cw_datagrid <?php echo $this->data['GridSettings']['HtmlClass']?>" id="<?php echo $this->data['GridSettings']['HtmlId']?>">


<thead>
    <tr>
		<?php 
		    if(!empty($this->data['GridSettings']['Fields']))
		        foreach($this->data['GridSettings']['Fields'] as $Field){
	
			    //If the Current Field is the Sort Index: 
			    if($this->data['GridSettings']['OrderField'] == $Field['id']){
			        if($this->data['GridSettings']['OrderMethod'] == 'ASC'){
			            $link = CobWeb::o('Router')->getCustomRoute(array('seq' => 'desc'));
			        }else{
			            $link = CobWeb::o('Router')->getCustomRoute(array('seq' => 'asc'));
			        }
			    }else{
			            $link = CobWeb::o('Router')->getCustomRoute(array('o'=>$Field['id'],'seq' => 'asc'));
			    }
		?>
		<td><a href="<?php echo $link; ?>" title="Nach <?php 
		            if($Field['displayName'] != false)
		             echo $Field['displayName']; 
		            else
		             echo $Field['id'];  
		    ?> sortieren"><?php 
		            if($Field['displayName'] != false)
		             echo $Field['displayName']; 
		            else
		             echo $Field['id'];  
		    ?></a>
		</td>
		
		<?php }
		if(!empty($this->data['GridSettings']['OptionRoutes']))
		if(count($this->data['GridSettings']['OptionRoutes']) > 0){
		$optionRouteColspan = count($this->data['GridSettings']['OptionRoutes']);
		?>
		<td colspan="<?php echo $optionRouteColspan; ?>">Optionen</td>
		<?php
		}
		?>
    </tr>
</thead>

<tbody>
	<?php
        $counter = 0;
	    foreach($this->data['GridData'] as $Dataset) {
		    //Odd or Even?
		    $counter++;
			if($counter % 2 == 0){
			    $class = 'even';
			}else{
			    $class = 'odd';
			}
			if($counter % 50 == 0){
			    ?>
			    
			    <thead>
    <tr>
		<?php 
		    if(!empty($this->data['GridSettings']['Fields']))
		        foreach($this->data['GridSettings']['Fields'] as $Field){
	
			    //If the Current Field is the Sort Index: 
			    if($this->data['GridSettings']['OrderField'] == $Field['id']){
			        if($this->data['GridSettings']['OrderMethod'] == 'ASC'){
			            $link = CobWeb::o('Router')->getCustomRoute(array('seq' => 'desc'));
			        }else{
			            $link = CobWeb::o('Router')->getCustomRoute(array('seq' => 'asc'));
			        }
			    }else{
			            $link = CobWeb::o('Router')->getCustomRoute(array('o'=>$Field['id'],'seq' => 'asc'));
			    }
		?>
		<td><a href="<?php echo $link; ?>" title="Nach <?php 
		            if($Field['displayName'] != false)
		             echo $Field['displayName']; 
		            else
		             echo $Field['id'];  
		    ?> sortieren"><?php 
		            if($Field['displayName'] != false)
		             echo $Field['displayName']; 
		            else
		             echo $Field['id'];  
		    ?></a>
		</td>
		
		<?php }
		if(!empty($this->data['GridSettings']['OptionRoutes']))
		if(count($this->data['GridSettings']['OptionRoutes']) > 0){
		$optionRouteColspan = count($this->data['GridSettings']['OptionRoutes']);
		?>
		<td colspan="<?php echo $optionRouteColspan; ?>">Optionen</td>
		<?php
		}
		?>
    </tr>
</thead>
			    
			    <?php 
			}
	    	?>
			<tr class='<?php echo $class ?>'>
			<?php foreach($this->data['GridSettings']['Fields'] as $Col) {
			     
			    if($Col['id'] == $this->data['GridSettings']['OrderField'])
			    {
			        $colClass='class="ordered"';
			    }else{
			        $colClass='class=""';
			    }
			    ?>
				
				<td <?php echo $colClass?>>
<?php 
			    // Type Switch:
			    if ($Col['type'] == 'escape') {
                    echo htmlentities($Dataset->$Col['id']);
                } elseif ($Col['type'] == 'varchar') {
                    echo ($Dataset->$Col['id']);
                } elseif ($Col['type'] == 'datetime') {
                    if ($Dataset->$Col['id'] == 0) {
                        echo '-'; 
                    } else {
                        echo date(cw_datestr,$Dataset->$Col['id']);
                    }
                } elseif ($Col['type'] == 'checked') {
                    if ($Dataset->$Col['id'] == 1) {
                        //TODO IMAGES!!
                        echo '<img src="/images/icons/icon_accept.gif" alt="1" />';
                    } else {
                        echo '<img src="/images/icons/action_stop.gif" alt="0" />';
                    }
                } elseif ($Col['type'] == 'ichecked') {
                    if ($Dataset->$Col['id'] == 1) {
                        echo '<img src="/images/icons/action_stop.gif" alt="1" />';
                    } else {
                        echo '<img src="/images/icons/icon_accept.gif" alt="0" />';
                    }
                } elseif ($Col['type'] == 'varchar_shorten') {
                    if(strlen($Dataset->$Col['id']) > 150){
                     echo  nl2br(substr($Dataset->$Col['id'], 0, 150))."...";
                    }else{
                        echo substr($Dataset->$Col['id'], 0, 150);
                    }
                
                } elseif ($Col['type'] == 'yesno') {
                    if ($Dataset->$Col['id'] == 1) {
                        echo 'Ja';
                    } else {
                        echo 'Nein';
                    }
                } elseif ($Col['type'] == 'iyesno') {
                    if ($Dataset->$Col['id'] == 1) {
                        echo 'Nein';
                    } else {
                        echo 'Nein';
                    }
                } elseif ($Col['type'] == 'mailto') {
                    echo '<a href="mailto:' . $Dataset->$Col['id'] . '">' . $Dataset->$Col['id'] . '</a>';
                } elseif ($Col['type'] == 'link') {
                    echo '<a href="' . $Dataset->$Col['id'] . '"><img src="/images/icons/page_next.gif" alt="Link" /></a>';
                } elseif (is_array($Col['type'])) { //ENUM VALUE ARRAY
                    echo $Col['type'][$Dataset->$Col['id']];
                } else {
                    if (function_exists("DG_Parse_" . $Col['type'])) {
                        echo call_user_func("DG_Parse_" . $Col['type'], $Dataset->$Col['id']);
                    } else {
                        echo htmlentities($Dataset->$Col['id']);
                    }
                }
			    ?>
				</td>
		<?php }
		if(count($this->data['GridSettings']['OptionRoutes'])>0){
		
		    foreach($this->data['GridSettings']['OptionRoutes'] as $optionRoute){
		        //Prepare Link:
		        $arrayspecParams = array();
		        foreach($optionRoute['referenceParams'] as $Param){
		            $arrayspecParams[$Param] = $Dataset->$Param;
		        }
		        
		        $customRoute = CobWeb::o('Router')->getCustomRoute($arrayspecParams,$optionRoute['route']);
		        
		        ?>
			<td><a href="<?php echo $customRoute; ?> "><?php echo $optionRoute['displayText']?></a></td>
		<?php
		    }
		}
		?>
			</tr>
	<?php } ?>

</tbody>

<tfoot>
    <tr class="sum">
        <td colspan="<?php echo count($this->data['GridSettings']['Fields']) + count($this->data['GridSettings']['OptionRoutes']) ?>">
        Datensätze: <?php echo $this->data['GridSettings']['ViewingFrom']; ?> bis  
        			<?php echo $this->data['GridSettings']['ViewingTo']; ?>
        Insgesamt: <?php echo $this->data['GridSettings']['Sum']; ?>
        <a href="<?php echo CobWeb::o('Router')->getSelfLink() ?>" title="Seite neu Laden">Neu Laden</a>
        </td>
         </tr>
        <tr class="limits_settings">
        <td colspan="<?php echo count($this->data['GridSettings']['Fields']) ?>">
	    	<script type="text/javascript">
	    	$(document).ready(function(){
		    	$('#<?php echo $this->data['GridSettings']['HtmlId']?> .limiter').submit(function (){return false});
		    	$('#<?php echo $this->data['GridSettings']['HtmlId']?> .limit').change(function() {window.location.replace(this.value)});
	    	});
	    	</script>
	    	<form class="limiter">
	    	<label for="<?php echo $this->data['GridSettings']['HtmlId']?>_limiter">Anzahl Datensätze</label>
		    	<select name="<?php echo $this->data['GridSettings']['HtmlId']?>_limiter" class="limit">
	    	    <?php 
	    		foreach($this->data['GridSettings']['Limiter'] as $value){
	    		    if($value >= $this->data['GridSettings']['Sum']){ 
	    		    $link =  CobWeb::o('Router')->getCustomRoute(array('l' => $value,'p'=> 1));
	    		    }else{
	    		    $link =  CobWeb::o('Router')->getCustomRoute(array('l' => $value));    
	    		    }
	    		    ?>
	    		    <option 
	    		    <?php if ($value == $this->data['GridSettings']['CurrentLimit']){?> selected = "selected"<?php }?>
	    		     value="<?php echo $link ?>"><?php echo $value ?></option>
	    		    <?php 
	    		}
	    		?>
	    		</select>
	    	</form>	    	
	    	</td>
    </tr>
    
    <tr class="pagination">
    	<td colspan="<?php echo count($this->data['GridSettings']['Fields']) ?>">
	    	<ol>
	    	<?php for($i=1;$i<=$this->data['GridSettings']['Pages'];$i++){ ?>
	    			<?php if($i == $this->data['GridSettings']['Page'])  { ?>
		    			<li class="current"> 
		    			<a href="#" title="Aktuelle Seite">
			    			<?php echo $i ?>
			    		</a>    			
	    			<?php } else { ?>
	    				<li class="current">
		    			<a href="<?php echo CobWeb::o('Router')->getCustomRoute(array('p' => $i)) ?>" title="Seite <?php echo $i?> von <?php echo $this->data['GridSettings']['Pages'] ?>">
			    			<?php echo $i ?>
			    		</a>
		    		<?php } ?> 
	    		</li>
	    	<?php }?>
	    	</ol>
    	</td>
    </tr>
</tfoot>
</table>