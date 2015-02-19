<script id="person-template" type="text/x-handlebars-template">
    <div class='person' id='{{id}}' >
        <div class='row' id='personHeader_{{id}}'>
            <div class='col-md-1'>
                <img src='{{avatar}}' class='avatar img-circle'>
            </div>
            <div class='col-md-11'><h3>{{name}}</h3></div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <table id=personTable_{{id}} class='table table-striped'></table>
            </div>
        </div>
</script>

<script id="table-header-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}>">
        <th class="col-md-2" data-type="project">Project</th>
        <th class="col-md-4" data-type="content">Description</th>
        <th class="col-md-1" data-type="due_on">Date</th>
    </tr>
</script>

<script id="table-entry-template" type="text/x-handlebars-template">
    <tr>
        <td class='col-md-2'>{{project}}</td>
        <td class='col-md-4'><a href='{{link}}' target='_blank'>{{content}}</a></td>
        <td class='col-md-1'>{{date due_on}}</td>
    </tr>
</script>