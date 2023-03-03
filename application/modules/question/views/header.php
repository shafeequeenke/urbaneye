<style type="text/css">
	.q-list-item ul li {
		list-style: none !important;
	}

	.q-list-left i {
		font-size: 24px;
	}

	.mcq-options input[type="radio"] {
	  	position: absolute;
		margin-left: -20px;
		margin-top: -10px
	}

	.mcq-options label {
	  	display: inline-block;
		background-color: none;
		font-size: 16px;
		margin: 5px 20px;
		min-width: 182px;
		padding: 5px 10px;
		font-weight: bold;
	 }

	.mcq-options input[type="radio"]:checked+label {
		background-color: #bbbbbb94;
		border: 1px solid #ccc;
		margin: 5px;
		min-width: 182px;
		padding: 5px 10px;
	}

	.mcq-ans-submit {
		display: none;
	}

	.option-save-btn {
	    font-size: 30px;
	    color: #0364b3;
	    padding-left: 130px;
	}

	.mcq-ans-submit:hover {
		cursor: pointer;
	}

	/*for create questions*/
	.add-question-wrapper {
		font-size: 20px;
		/*border: 3px solid #efecec; */
		margin-bottom: 10px;
	}
	.add-form {
		margin-top: 10px;
		padding: 5px;
	}

	.multiple-choice {
		display: none;
	}

	.mcq-opt input[type=text] {
		min-width: 300px;
	}

	.warning-msg {
		display: none;
		color: orange;
	}

	.success-msg {
		display: none;
		color: green;	
	}

	.add-question-console {
		overflow-x: auto;
		max-height: 700px;
		max-width: 96%;
		margin-left: 0px;
	}

	.similar-question-list {
		list-style: circle;
	}

	.reset-form {
		display: none;
	}

	.q-list-text a {
		text-decoration: none;
		transform: none;
		line-height: 28px !important;
	}

	.container {
		padding: 10px 0px !important;
	}

	.q-single-answer li {
		border-bottom: 1px solid #f0f0f5;
	}


	.q-single-answer li:hover {
		border-bottom: 3px solid #f0f0f5;
	}

	.search-tags li {
		list-style: none;
		float: left;
		margin: 5px 5px;
		background: #e7e7e7;
		line-height: 30px;
		padding: 5px 8px;
		cursor: pointer;
	}

	.add-answer .q-list-text .add-form {
		/*width: 50%;*/
	}
	
	.user-answer-dup {
		margin-top: 50px;
		display: none;
	}

	.user-answer-dup .row .container {
		max-width: 99% !important;
	}

	.user-answer-dup .row .container .row {
		max-width: 99% !important;
    	overflow-x: auto;
	}


	/*create question check box button*/
	.switch_box{
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		max-width: 500px;
		min-width: 200px;
		-webkit-box-pack: center;
		    -ms-flex-pack: center;
		-webkit-box-align: center;
		    -ms-flex-align: center;
		        align-items: center;
		-webkit-box-flex: 1;
		    -ms-flex: 1;
		        flex: 1;
	}
	
/* Switch  Specific Styles Start */
	input[type="checkbox"].switch_1{
		font-size: 20px;
		-webkit-appearance: none;
		   -moz-appearance: none;
		        appearance: none;
		width: 65px;
		height: 32px !important;
		background: none;
		border:1px solid #717171;
		border-radius: 3em !important;
		min-height: 0px !important;
		position: relative;
		cursor: pointer;
		outline: none;
		-webkit-transition: all .2s ease-in-out;
		transition: all .2s ease-in-out;
	  }
	  
	  input[type="checkbox"].switch_1:checked{
		background: #0ebeff;
	  }
	  
	  input[type="checkbox"].switch_1:after{
		position: absolute;
		content: "";
		width: 1.5em;
		height: 1.5em;
		border-radius: 50%;
		background: #fff;
		-webkit-box-shadow: 0 0 .25em rgba(0,0,0,.3);
		        box-shadow: 0 0 .25em rgba(0,0,0,.3);
		-webkit-transform: scale(.7);
		        transform: scale(.7);
		left: 0;
		-webkit-transition: all .2s ease-in-out;
		transition: all .2s ease-in-out;
	  }
	  
	  input[type="checkbox"].switch_1:checked:after{
		left: calc(100% - 1.5em);
	  }
	  .switch_box label {
	  	margin-left: 15px;
	  }

	  /*.radio-opt label i {
		    position: relative;
			float: right;
			cursor: pointer;
			width: 39px;
			color: #fff;
			text-shadow: 0 -1px 0 rgba(0, 0 ,0, .3);
			top: -33px;
		}*/

	.radio-opt .cke_contents {
		height: 75px !important;
	}
	/* Switch  Specific Style End */
	.q-list-right label i {
		cursor: pointer;
	}

	.q-list-left i {
		color: #717171;
	}

	.question-text-area {
		min-height: 250px;
	}

	/*ckeditor custom for create /edit question*/
	.cke_button__image_icon , .cke_button__about_icon , .cke_button__table {
		display: none !important;
	}
	/*ckeditor custom for create /edit question ends*/
	/*latex editor start*/
	.question-container textarea {
		
	}
	/*latex editor ends*/
	/*plain text editor starts*/
	.upload-question-console {
		border:solid #ccc 1px;
		background: #e7e7e7;
		max-width: 96%;
	}
	.file-drop-area {
	  position: relative;
	  display: flex;
	  align-items: center;
	  width: 450px;
	  max-width: 100%;
	  padding: 25px;
	  border: 1px dashed rgba(255, 255, 255, 0.4);
	  border-radius: 3px;
	  transition: 0.2s;
	  &.is-active {
	    background-color: rgba(255, 255, 255, 0.05);
	  }
	}

	.fake-btn {
	  flex-shrink: 0;
	  background-color: rgb(3, 100, 179);
	  border: 1px solid rgba(255, 255, 255, 0.1);
	  border-radius: 3px;
	  padding: 8px 15px;
	  margin-right: 10px;
	  font-size: 12px;
	  text-transform: uppercase;
	  color: #fff;
	}

	.file-msg {
	  	font-size: small;
	  	font-weight: 300;
	  	line-height: 1.4;
	  	white-space: nowrap;
	  	overflow: hidden;
	  	text-overflow: ellipsis;
	  	color: #635858c7;
	  	font-weight: bold;
	}

	.file-input {
	  position: absolute;
	  left: 0;
	  top: 0;
	  height: 100%;
	  width: 100%;
	  cursor: pointer;
	  opacity: 0;
	  &:focus {
	    outline: none;
	  }
	}
	/*plain text editor ends*/

</style>