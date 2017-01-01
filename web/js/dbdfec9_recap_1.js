$(document).ready(function () {
    var table = $('#tabRecap').DataTable({
        "sAjaxSource": Routing.generate('panier_data'),
        "aoColumns": [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {"mDataProp": "libelleProduit", "width": "80"},
            {"mDataProp": "libelleProduit"},
            {"mDataProp": "prixProduit"},
            {"mDataProp": "qteProduit"},
            {"mDataProp": "qteProduit"}
        ],
        "bFilter": false,
        "paging": false,
        "bSort": false,
        "oLanguage": {"sProcessing": "Traitement en cours...",
            "sLengthMenu": "",
            "sZeroRecords": "Votre panier est vide",
            "sInfo": "",
            "sInfoEmpty": "",
            "sInfoFiltered": "(filtré de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sSearch": "Rechercher:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Précédent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            }
        },
        "createdRow": function (row, data, index) {
                     if (data["typeProduit"] == "Boisson" || data["typeProduit"] == "Dessert")
                $('td', row).eq(0).removeClass("details-control");

            var imgProduit = "/burger/web/" + data["imageProduit"];
            $('td', row).eq(1).html("<img src='" + imgProduit + "' width='80'/>");
            $('td', row).eq(2).html("<a href=''>" + data["libelleProduit"] + "</a><br>" + data["descriptionProduit"]);
            $('td', row).eq(3).html(data["prixProduit"] + " €");
            var op = data
            $('td', row).eq(4).html('<i class="fa fa-plus-circle fa-lg" aria-hidden="true" onClick="incrementerProduitPanier(' + index + ')"  onmouseover="this.style.cursor=\'pointer\'""></i> <i class="fa fa-minus-circle fa-lg" aria-hidden="true"   onmouseover="this.style.cursor=\'pointer\'" onClick="reduireProduitPanier(' + index + ')"></i> <input type="text" id="inputTextQuantite" value="' + data['qteProduit'] + '"  readonly/> <i class="fa fa-times fa-lg" aria-hidden="true"  onmouseover="this.style.cursor=\'pointer\'" onClick="supprimerProduitPanier(' + index + ')"></i>');
            $('td', row).eq(5).html(data["prixProduit"] * data["qteProduit"] + " €");
            if ( (data["typeProduit"] == "Burger" || data["typeProduit"] == "Woop" || data["typeProduit"] == "Sandwich") && data["optionsProduit"][0] == "") { // burger seul
                $('td', row).eq(0).removeClass("details-control");
                $('td', row).eq(2).html("<a href=''>" + data["libelleProduit"] + "</a> ("+data["typeProduit"]+" seulement)<br>" + data["descriptionProduit"]);
            }
            else
            {
                $('td', row).eq(2).html("<a href=''>" + data["libelleProduit"] + "</a><br>" + data["descriptionProduit"]);
            }
        }
    });
    
     // Add event listener for opening and closing details
    $('#tabRecap tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data().typeProduit, row.data().optionsProduit)).show();
            tr.addClass('shown');
        }
    });
     function format(type, options) {
        var retour = "";
        var i = 0;
        retour += "<ul>";

        if (type == "Burger" || type == "Woop" || type == "Sandwich")
        {
            retour += "<li> Frite: " + options[0] + "</li>";
            retour += "<li> Sauce 1: " + options[1] + "</li>";
            retour += "<li> Sauce 2: " + options[2] + "</li>";
            retour += "<li> Boisson: " + options[3] + "</li>";
            if ((type == "Burger" || type == "Woop") && options[5] != "-1")
                retour += "<li> Informations supplémentaires: " + options[5] + "</li>";
            if (options[4] != -1) {
                retour += "<li> Supplement: ";
                for (i = 0; i < options[4].length; i++) {
                    retour += options[4][i];
                    if (i != options[4].length - 1)
                        retour += ", ";
                }
                retour += "</li>";
            }

            retour += "</ul>";
        }
        if (type == "Sandwich")
        {
            retour += "<li> Crudités: ";
            for (i = 0; i < options[5].length; i++) {
                retour += options[5][i];
                if (i != options[5].length - 1)
                    retour += ", ";
            }
            retour += "</li>";
        }
        if (type == "Tex mex")
        {
            retour += "<li> Sauce:" + options[1] + "</li> ";
            if (options[4] != -1) {
                retour += "<li> Supplement: ";
                for (i = 0; i < options[4].length; i++) {
                    retour += options[4][i];
                    if (i != options[4].length - 1)
                        retour += ", ";
                }
                retour += "</li>";
            }
        }
        retour += "</ul>";

        return retour;
    }


});