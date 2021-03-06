
var baseUrl = location.protocol + "//" + location.host;
/**Get all smiles for HTML text editor*/
 function getAllSmiles(extra_smiles) {
    var path = 'emoticons/smiles/';
    var smile = 's';
    var extension = '.gif';
    var qty = 63;
    var smilesObject = {};
    var key;
    var result;

    for (var i = 0; i <= qty; i++) {
        key = ':'+ smile + i+':';
        result = path + smile + i + extension;
        smilesObject[key] = result;
    }

    /**Get extra smiles */
    for (var i = 0; i < extra_smiles.length; i++) {
        key = extra_smiles[i]['charactor'] ;
        result = path + extra_smiles[i]['filename']
        smilesObject[key] = result;
    }
    return smilesObject;
}

/**Get additional smiles for HTML text editor*/
function getMoreSmiles() {
    var path = 'emoticons/smiles/';
    var smilesObject = {
        ':silver:': path + 'silver.png',
        ':terran:': path + 'terran.gif',
        ':zerg:': path + 'zerg.gif',
        ':gold:': path + 'gold.png',
        ':protoss:': path + 'protoss.gif',
        ':random:': path + 'random.png'
    };
    return smilesObject
}

/**race's images array for custom command of HTML text editor SCEditor */
function getRacesImg() {
    return ['terran.gif','zerg.gif','protoss.gif','random.png'];
}

/**countries's code array for custom command of HTML text editor SCEditor */
function getCountries() {
    return [
        'RU',
        'KR',
        'KZ',
        'BY',
        'PL',
        'UA',
        'UZ',
        'CN',
        'TW',
        'GR',
        'AL',
        'DZ',
        'AD',
        'AO',
        'AR',
        'AM',
        'AU',
        'AT',
        'AZ',
        'BS',
        'BH',
        'BD',
        'BB',
        'AF',
        'CF',
        'BE',
        'BZ',
        'BJ',
        'BT',
        'BO',
        'BA',
        'BW',
        'BR',
        'BN',
        'BG',
        'BF',
        'BI',
        'KH',
        'CM',
        'CA',
        'CV',
        'CL',
        'CO',
        'CG',
        'HR',
        'CU',
        'CY',
        'CZ',
        'DK',
        'EC',
        'EG',
        'ER',
        'EE',
        'ET',
        'EU',
        'FJ',
        'FI',
        'FR',
        'GA',
        'GE',
        'DE',
        'GT',
        'GN',
        'GY',
        'HT',
        'HK',
        'HR',
        'HU',
        'IS',
        'IN',
        'ID',
        'IR',
        'IQ',
        'IE',
        'IL',
        'IT',
        'CI',
        'JM',
        'JP',
        'JO',
        'KE',
        'KP',
        'KG',
        'LV',
        'LB',
        'LY',
        'LT',
        'LU',
        'MG',
        'MY',
        'MR',
        'MX',
        'MD',
        'MC',
        'MN',
        'MA',
        'MZ',
        'NA',
        'NR',
        'NP',
        'NL',
        'NZ',
        'NO',
        'OM',
        'PK',
        'PA',
        'PY',
        'PE',
        'PH',
        'PT',
        'QA',
        'RO',
        'SA',
        'RS',
        'SL',
        'SG',
        'SK',
        'SI',
        'SO',
        'ZA',
        'ES',
        'SD',
        'SE',
        'CH',
        'TZ',
        'TW',
        'TH',
        'TG',
        'TO',
        'TT',
        'TN',
        'TR',
        'TV',
        'UK',
        'US',
        'UG',
        'UY',
        'VE',
        'VN',
        'YE',
        'ZW'
    ];
}

/**
 * Add custom command: "races" into HTML text editor SCEditor
 * https://www.sceditor.com/posts/how-to-add-custom-commands/
 * https://www.sceditor.com/
 * */
function addRaces() {
    sceditor.command.set("races", {
        exec: function (caller) {
            // Store the editor instance so it can be used
            // in the click handler
            var races = getRacesImg();
            var editor = this;
            var $content = $("<div />");
            // Create country flags options
            for (var i = 0; i < races.length; i++) {
                $(
                    '<img src="/images/emoticons/smiles/'+races[i]+'" alt="">'
                )
                    .data('race', races[i])
                    .click(function (e) {
                        var image_url = baseUrl + '/images/emoticons/smiles/'+$(this).data('race');
                        editor.insert('[img]'+image_url+'[/img]');
                        editor.closeDropDown(true);

                        e.preventDefault();
                    })
                    .appendTo($content);
            }
            editor.createDropDown(caller, "race-picker", $content.get(0));
        },
        tooltip: "Race"
    });
}

/**
 * Add custom command: "countries" into HTML text editor SCEditor
 * https://www.sceditor.com/posts/how-to-add-custom-commands/
 * https://www.sceditor.com/
 * */
function addCountries() {
    sceditor.command.set("countries", {
        exec: function (caller) {
            // Store the editor instance so it can be used
            // in the click handler
            var flags = getCountries().map(function (value) {
                return value.toLowerCase();
            });
            var editor = this;
            var $content = $("<div />");
            // Create country flags options
            for (var i = 0; i < flags.length; i++) {
                $(
                    '<span class="flag-icon flag-icon-'+flags[i]+'" title="'+flags[i]+'"></span>'
                )
                    .data('flag', flags[i])
                    .click(function (e) {
                        var image_url = baseUrl + '/flags/editor/'+$(this).data('flag')+'.png';
                        editor.insert('[img]'+image_url+'[/img]');
                        editor.closeDropDown(true);

                        e.preventDefault();
                    })
                    .appendTo($content);
            }
            editor.createDropDown(caller, "country-picker", $content.get(0));
        },
        tooltip: "Countries flags"
    });
}
function addUpload() {
    sceditor.command.set("upload", {
        exec: function (caller) {
            var editor = this;
            var content = document.createElement("DIV");
            var div = '<label class="prev_imgs">All Images</label>'+
                      '<form id="upload_form"><label for="upload">Upload</label> ' +
                      '<input type="file" id="upload" dir="ltr" style="max-width: 100%;" /></div>' +
                      '<div><input type="button" class="button" value="Upload" />' +
                      '</form>';
            $(content).append(div);
            $(content).on("click", '.prev_imgs', function(){
                $("body").addClass('upload-overlay-open')
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/forum/topic/get_prev_images',
                    contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                    processData: false, // NEEDED, DON'T OMIT THIS
                    data: [],
                    datatype: 'JSON',
                    success: function (result) {
                        $(".all_images").append(result)
                    },
                    error: function (e) {
                        console.log(e)
                    }
                });

            });

            $("body").on('click', '.upload-overlay .showImages .close_overlay', function(e) {
                $(".all_images").children().remove()
                $("body").removeClass('upload-overlay-open')
            })

            $("body").on('click', '.upload-overlay .showImages .open_img', function(e){
                $(".prev_image").each(function(index){
                    if($(this).find('input[type=checkbox]').prop("checked")) {
                        var image_url = baseUrl + $(this).find('img').attr('src');
                        editor.insert('[img]'+image_url+'[/img]');
                    }
                })
                $(".all_images").children().remove()
                $("body").removeClass('upload-overlay-open')
                editor.closeDropDown(true);
                e.preventDefault();
            })
            $(content).on('click', '.button', function (e) {
                var input = $(content).find("#upload")[0];
                if (input.value) {
                    if (input.files && input.files[0]) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        var formdata = new FormData();
                        formdata.append("file", input.files[0]);
                        $.ajax({
                            type: 'POST',
                            url: '/forum/topic/img_upload',
                            data: formdata,
                            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                            processData: false, // NEEDED, DON'T OMIT THIS
                            success: function (result) {
                                if (result.success) {
                                    var image_url = baseUrl + result.data;
                                    editor.insert('[img]'+image_url+'[/img]');
                                } else {
                                    alert(result.data) //
                                }
                            },
                            error: function (e) {
                                console.log(e)
                            }
                        });
                    }
                } else {
                    alert("Please select file");
                }
                editor.closeDropDown(true);
                e.preventDefault();
            });

            editor.createDropDown(caller, 'uploadimage', content);
        },
        tooltip: 'Upload Image'
    });
}


function addStream() {
    sceditor.command.set("streams", {
        exec: function (caller) {
            var	editor  = this;
            var content =document.createElement("DIV");
            var div =
                '<label for="link" unselectable="on">Stream URL:</label> '+
                '<input type="text" id="stream" dir="ltr" placeholder="https://">'+
                '</div>'+
                '<div unselectable="on">'+
                '<label for="width" unselectable="on">Ширина (необязательно):</label>'+
                '<input type="text" id="width" size="2" dir="ltr">'+
                '</div>'+
                '<div unselectable="on">'+
                '<label for="height" unselectable="on">Высота (необязательно):</label>'+
                '<input type="text" id="height" size="2" dir="ltr">'+
                '</div>'+
                '<div unselectable="on">'+
                '<input type="button" class="button" value="Вставить">'
                ;
            $(content).append(div);
            $(content).on('click', '.button', function (e) {
                var	input = $(content).find("#stream");
                if(input.val()) {
                    var width = ($('#width').val()) ? $('#width').val() : '640';
                    var height = ($('#height').val()) ? $('#height').val() : '510';
                    editor.insert('<iframe src="'+input.val()+'" height="'+height+'" width="'+width+'" frameborder="0" scrolling="no" allowfullscreen="true">'+
                    '</iframe>');
                }
                editor.closeDropDown(true);
                e.preventDefault();
            })
            editor.createDropDown(caller, "insertstream", content);
        },
        tooltip: "Video Stream"
    });
}

function addSpoiler() {
    $.sceditor.command.set("spoiler", {
        exec: function(caller, html) {
            var	before = '[spoiler]',
                end    = '[/spoiler]';

            // if there is HTML passed set end to null so any selected
            // text is replaced
            if (html) {
                before = before + html + end;
                end    = null;
                // if not add a newline to the end of the inserted quote
            } else if (this.getRangeHelper().selectedHtml() === '') {

            }
            this.wysiwygEditorInsertHtml(before, end);
        },
        tooltip: "Spoiler"
    });
}
