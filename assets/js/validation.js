    $(document).ready(function() {
		$.validator.addMethod('filesize', function(value, element, param) {
			// param = size (en bytes) 
			// element = element to validate (<input>)
			// value = value of the element (file name)
			return this.optional(element) || (element.files[0].size <= param) 
		});
        $("#createorupdate").validate({
            rules: {
                county: {
                    required: true,
                    minlength: 2,
                    maxlength: 60
                },
                country: {
                    required: true,
                    minlength: 2,
                    maxlength: 60
                },
                postcode: {
                    minlength: 4,
                    maxlength: 6,
                    number: true
                },
                description: {
                    required: true
                },
                address: {
                    required: true,
                    minlength: 15,
                    maxlength: 150
                },
                town: {
                    required: true,
                    minlength: 2,
                    maxlength: 60
                },
                num_bedrooms: {
                    required: true,
                    number: true
                },
                num_bathrooms: {
                    required: true,
                    number: true
                },
                price: {
                    required: true,
                    number: true
                },
                property_type_id: {
                    required: true,
                    number: true
                },
                type: {
                    required: true
                },
				image: {
                    filesize: 5000000
                }
				
            },
            messages: {
                description: {
                    required: "Please enter a detail about property",
                },
                address: {
                    required: "Please enter a address of property",
                    minlength: "Your password must be at least 15 characters long"
                },
                num_bedrooms: {
                    required: "Please select number of bedroom available for property",
                },
                num_bathrooms: {
                    required: "Please select number of bathroom available for property",
                },
                property_type_id: {
                    required: "Please select property type"
                },
				image : { 
					filesize: "Please select a file less than 5mb"
				}
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "type") {
                    $(".error-type-message").html(error);
                } else {
                    error.insertAfter(element);
                }

            }
        });
    });