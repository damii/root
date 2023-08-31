function azu_toggle_change($this) {
if($this.checked) {
	if($this.value == 'no')
		$this.value='yes'; 
	else
		$this.value=true; 
}
else {
	if($this.value == 'yes')
		$this.value='no';
	else
		$this.value=false;
}
}