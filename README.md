# How to use Kirk's password strength meter



1. Load Kirk's dependencies. Most important are these ones:

```
<link href="/hack_the_password/phramewrk/styles/forms.css" rel="stylesheet" type="text/css"> <!-- or some instance of forms.css -->

<script src="/hack_the_password/phramewrk/scripts/jQuery.js" type="text/javascript" language="javascript"></script>
<script src="/hack_the_password/phramewrk/scripts/Class.js" type="text/javascript" language="javascript"></script>
<script src="/hack_the_password/phramewrk/scripts/Form.js" type="text/javascript" language="javascript"></script>
<script src="/hack_the_password/phramewrk/scripts/Phramewrk.js" type="text/javascript" language="javascript"></script>
```

1. Set treatment

```
# This line is in `PasswordSecurityStudy-socwall.js`
this.tipType = treatment.type; // globally set before initialization of this class
```

e.g., 

```
<script type="text/javascript" language="javascript">
    treatment = {
                    "type" : "{{ treatment.type }}",
                    "is_interactive" : "{{ treatment.is_interactive }}"
                };
</script>
<script src="/hack_the_password/scripts/PasswordSecurityStudy-socwall.js" type="text/javascript" language="javascript"></script>
```

2. provide a `#pssResults` div for the static text to be inserted into

```
{% if treatment.is_interactive %}
    <input id="updatePassword" name="updatePassword" tabindex="2" accesskey="p" type="password" value="" onkeyup="passwordSecurityStudy.getTip(this, $(this).val(), '{{ treatment.type }}' );" >
{% else %}
    <input id="updatePassword" name="updatePassword" tabindex="2" accesskey="p" type="password" value="" >
{% endif %}

{% if not (treatment.type == 'control') %}
<div id="updatePassword-tip" style="display: none;" class="formComponentTip">
    <div id="pssResults">
        <p>Please enter a password.</p>
    </div>
</div>
{% endif %}

```

3. Make a call to `Form`

Note the need to properly name the id of your elements. That is to say, your form html needs to follow a certain structure, with wrappers, page, section, components. 

```
<script type="text/javascript" language="javascript">
    $(document).ready(function () {                    
        passwordAnalyzerObject = new Form('passwordAnalyzer', 
            {"options":{
                    "submitButtonText":"Submit",
                },
                "formPages":{
                    "updatePassword-page1":{
                        "formSections":{
                            "updatePassword-page1-section1":{
                                "formComponents":{
                                    "updatePassword":{
                                        "options": {
                                            "validationOptions":["required"]
                                        },
                                        "type":"FormComponentSingleLineText"
                                    },
                                    "updateConfirmPassword":{
                                        "options" :{
                                            "validationOptions":{
                                                "matches":"updatePassword",
                                                "0":"required"
                                            }
                                        },
                                        "type":"FormComponentSingleLineText"
                                    },
                                    "updatePassword-view":{
                                        "type":"FormComponentHidden"
                                    },
                                    "updatePassword-viewData":{
                                        "type":"FormComponentHidden"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ); 
    });
</script>
```

# Valid tipTypes


`this.tipTypes = ['control','staticText','dynamicText','strengthMeter']; // removed dynamicTextAndStrengthMeter`

apparently `random` is also a legitimate tipType

