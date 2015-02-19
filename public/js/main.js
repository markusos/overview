
(function() {

    var tableData = [];
    var tableState = [];

    $('.nav').hide();

    $.getJSON("/api").done(function( data ) {
        $("#loading").hide();

        if (data.status === 'Not authenticated') {
            $("#connect").show();

            return;
        }

        $('.nav').show();

        var order = [];
        if (localStorage['listOrder']) {
            order = JSON.parse(localStorage['listOrder']);

            // Read in all persons from the api
            $.each(data, function( i, person ) {
                tableData[person.id] = person;
            });

            // Add the persons in the order stored in local storage
            $.each(order, function( i, personId ) {
                if (tableData[personId]) {
                    addPerson(tableData[personId]);
                }
            });
        }

        // add all persons not found in the order array at the end
        $.each(data, function( i, person ) {
            if ($.inArray(person.id, order) === -1)  {
                tableData[person.id] = person;
                addPerson(person);
            }
        });

        $("#todos" ).sortable({
            update: function (event, ui) {
                var list =  $(this).sortable("toArray");
                localStorage['listOrder'] = JSON.stringify(list);
            }
        });
    });

    Handlebars.registerHelper('date', function(date) {
        if (date === null) {
            return 'N/A';
        }
        else {
            return new Date(date + ' EST').toLocaleDateString("en-US");
        }
    });

    function addPerson(person) {

        var source = $("#person-template").html();
        var template = Handlebars.compile(source);
        var context = {id: person.id ,avatar: person.avatar, name: person.name};
        var html = template(context);

        $( html ).appendTo( "#todos" );

        tableState[person.id] = [];
        tableState[person.id]['sortBy'] = 'due_on';
        tableState[person.id]['ascending'] = true;
        tableState[person.id]['visible'] = false;

        generateTable(person.id, person, tableState[person.id]['sortBy'], tableState[person.id]['ascending']);

        $( "#personHeader_" + person.id ).click(function() {
            var table = $( "#personTable_" + person.id );
            table.toggle();
            localStorage['visible_' + person.id] = table.is(":visible");
        });

        if (localStorage['visible_' + person.id]) {
            tableState[person.id]['visible'] = JSON.parse(localStorage['visible_' + person.id]);
        }

        if (!tableState[person.id]['visible']) {
            $( "#personTable_" + person.id ).hide();
        }
    }

    function updateTable(personId, sortBy) {
        var ascending = true;

        if(sortBy === tableState[personId]['sortBy']) {
            ascending = tableState[personId]['ascending'] !== true;
        }

        $( "#personTable_" + personId ).empty();

        generateTable(personId, tableData[personId], sortBy, ascending);

        var direction = (ascending) ? 'up' : 'down';
        var html  = "<span class='glyphicon glyphicon-chevron-" + direction +  " aria-hidden='true'></span>";
        $("#personTable_" + personId + " tr th[data-type='" + sortBy + "']").append(html);

        tableState[personId]['sortBy'] = sortBy;
        tableState[personId]['ascending'] = ascending;
    }

    function generateTable(personId, person, sortBy, ascending) {
        var order = 1;
        if (!ascending) {
            order = -1;
        }

        person.todos.sort(function(a,b) {
            if (a[sortBy] === null) { return 1; }
            if (b[sortBy] === null) { return -1; }

            if (a[sortBy].toLowerCase() > b[sortBy].toLowerCase()) {
                return order;
            }
            else if (b[sortBy].toLowerCase() > a[sortBy].toLowerCase()) {
                return  -1 * order;
            }
            else {
                return 0;
            }
        } );

        var source = $("#table-header-template").html();
        var template = Handlebars.compile(source);
        var context = {id: personId};
        var html = template(context);

        source = $("#table-entry-template").html();
        template = Handlebars.compile(source);
        $.each(person.todos, function( i, todo ) {
            html += template(todo);
        });

        $( html ).appendTo(  "#personTable_" + personId  );

        $("#personTable_" + person.id + " tr th").click(function() {
            var type = $(this).attr('data-type');
            updateTable(personId , type);
        });
    }

})();
