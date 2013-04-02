<script src="../rjson.js" type="text/javascript" language="JavaScript"></script>
<script src="jquery.js" type="text/javascript" language="JavaScript"></script>
<script language="JavaScript" type="text/javascript">
	$.ajax({
		"dataType": "json",
		"type": "POST",
		"url": "DataProcess.php?action=get_data",
		"success": function(data) {

			console.log('Pack->result=');
			console.dir(data);

			var unpackData = RJSON.unpack(data);

			console.log('UnPack->result=');
			console.dir(unpackData);

			var packData = RJSON.pack(unpackData);

			$.ajax({
				"dataType": "json",
				"type": "POST",
				"url": "DataProcess.php?action=save_data",
				"data": {"pack_data": packData},
				"success": function(response) {
					//respond
					console.log(response);
				}
			});


		}
	});




</script>