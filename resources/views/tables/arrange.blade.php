@extends('mgmtlayout')
@section('title', 'Arrange Tables')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <style>
        span#table-count {
            background: #1F579C;
            color: #fff;
            cursor: context-menu;
            user-select: auto;
        }

        table {
            width: 0 !important;
            margin-left: auto;
            margin-right: auto;
            border-collapse: separate;
            border-spacing: 50px;
            background: #adbac9 !important;
        }

        td {
            height: 150px !important;
            position: relative;
            cursor: pointer;
            border: none;
            background: #fff !important;
            border-width: 3px !important;
            border-style: solid;
            border-color: transparent;
            transition: border-color 0.3s ease;
        }

        td.item-empty {
            background: #ddd !important;
        }

        img.item-img {
            width: 125px;
            background: transparent;
            /* margin-bottom: 1rem; */
        }

        div.status span {
            position: absolute;
            right: -10px;
            bottom: -10px;
            height: 30px;
            width: 30px;
            border-radius: 50%;
            display: inline-block;
            border-width: 3px;
            border-style: solid;
        }

        span.green {
            background: #55f340;
            border-color: #23d30c;
        }

        span.yellow {
            background: #fad53b;
            border-color: #dcb619;
        }

        span.red {
            background: #ff4444;
            border-color: #eb1515;
        }

        span.gray {
            background: #b9b9b9;
            border-color: #9c9c9c;
        }

        input[type="text"]:disabled {
            cursor: pointer;
            pointer-events: none;
        }

        input[type="text"],
        input[type="text"]:focus,
        input[type="text"]:disabled {
            background: transparent;
            border: transparent;
            box-shadow: none;
            text-align: center;
        }

        div.add {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s linear;
            position: absolute;
            font-size: 1.5rem;
        }

        div.add.visible {
            visibility: visible;
        }

        div.add.visible:hover {
            opacity: 1;
        }

        div.add.top {
            top: -40px;
            left: 30%;
        }

        div.add.bottom {
            bottom: -40px;
            left: 30%;
        }

        div.add.left {
            left: -40px;
            top: 30%;
        }

        div.add.right {
            right: -40px;
            top: 30%;
        }

        div.minus {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s linear;
            position: absolute;
            font-size: 1.5rem;
            color: var(--first-color);
        }

        div.minus.visible {
            visibility: visible;
        }

        div.minus.visible:hover {
            opacity: 1;
        }

        div.minus.disabled {
            cursor: not-allowed;
            filter: opacity(0.5);
        }

        div.minus.top {
            top: -40px;
            right: 30%;
        }

        div.minus.bottom {
            bottom: -40px;
            right: 30%;
        }

        div.minus.left {
            left: -40px;
            bottom: 30%;
        }

        div.minus.right {
            right: -40px;
            bottom: 30%;
        }

        .l-toolbar {
            position: fixed;
            top: calc(var(--header-height) + 1rem);
            right: -12%;
            width: 150px;
            height: calc(100% - (var(--header-height) + 1rem));
            background-color: #764A3D;
            transition: .5s;
        }

        .l-label {
            position: fixed;
            top: calc(var(--header-height) + 1rem);
            right: -20px;
            padding-right: 20px;
            width: 80px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top-left-radius: .25rem;
            border-bottom-left-radius: .25rem;
            background-color: #764A3D;
            transition: .5s;
        }

        .l-delete {
            position: fixed;
            top: calc(var(--header-height) + 5.5rem);
            right: -20px;
            padding-right: 20px;
            width: 80px;
            height: 50px;
            display: none;
            justify-content: center;
            align-items: center;
            border-top-left-radius: .25rem;
            border-bottom-left-radius: .25rem;
            background-color: #eb1515;
            transition: .5s;
        }

        .l-delete.hovered {
            transform: scale(1.2);
        }

        i#toolbar-toggle,
        i#toolbar-delete {
            font-size: 1.5rem;
            color: var(--first-color-light);
            cursor: pointer;
            vertical-align: middle;
        }

        .content {
            transition: .5s;
        }

        .content-pd {
            padding-right: 150px;
        }

        .showToolbar {
            right: 0;
        }

        .moveLabel {
            right: 130px;
        }

        i#toolbar-toggle.fa-times:before {
            content: "\f00d";
        }

        .l-toolbar nav {
            overflow-y: auto;
        }

        img.drag-img {
            width: 100px;
            background: var(--first-color-light);
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
        }

        .btn-success {
            background: #18ac31;
            border-color: #18ac31;
        }

        .btn-success:hover {
            background: #19bf35;
            border-color: #19bf35;
        }
    </style>
@endsection

@section('body')
    <div class="content" id="content-pd">
        <div class="d-flex">
            <a href="/tables" class="btn btn-secondary" role="button">
                <i class="fas fa-arrow-left"></i>&nbsp; Back to index
            </a>
            <button type="submit" class="btn btn-success ms-2" id="save">Save Changes</button>
        </div>

        <div class="d-flex justify-content-center mb-4">
            <span id="table-count" class="btn mx-auto"></span>
        </div>

        @php
            $status = ['occupied', 'available', 'reserved', 'out of service'];

            function getColorForStatus($status)
            {
                switch ($status) {
                    case 'occupied':
                        return 'red';
                    case 'available':
                        return 'green';
                    case 'reserved':
                        return 'yellow';
                    case 'out of service':
                        return 'gray';
                    default:
                        return '';
                }
            }

            $row = 0;
            $tableCounter = 0;
        @endphp

        <div class="table-responsive">

            <table class="table mb-4">

                @foreach ($tables as $table)
                    @php
                        // Check if status is predefined
                        $is_predefined = in_array($table->status, $status);
                    @endphp

                    @if ($table->row > $row)
                        @if ($row != 0)
                            {{-- Close previous row --}}
                            </tr>
                        @endif

                        {{-- Start new row --}}
                        <tr>
                            @php $row++; @endphp
                    @endif

                    <td class="text-center align-middle @if ($table->status == null) item-empty @endif"
                        id="{{ $table->id }}" ondrop="drop(event)" ondragover="allowDrop(event)" draggable="true"
                        ondragstart="drag(event)" ondragend="dragEnd()">

                        <div class="add top"><i class="fas fa-plus-circle"></i></div>
                        <div class="add bottom"><i class="fas fa-plus-circle"></i></div>
                        <div class="add left"><i class="fas fa-plus-circle"></i></div>
                        <div class="add right"><i class="fas fa-plus-circle"></i></div>

                        <div class="minus top"><i class="fas fa-minus-circle"></i></div>
                        <div class="minus bottom"><i class="fas fa-minus-circle"></i></div>
                        <div class="minus left"><i class="fas fa-minus-circle"></i></div>
                        <div class="minus right"><i class="fas fa-minus-circle"></i></div>

                        <div class="d-flex flex-column">
                            {{-- Show seat no only when status is predefined as above --}}

                            <div class="image">
                                @if ($is_predefined)
                                    <img class="item-img" src="/img/tables/seat-{{ $table->seat }}.png" />
                                    @php $tableCounter++; @endphp
                                @endif
                            </div>

                            <div class="status">
                                @if ($is_predefined)
                                    <span class="dot {{ getColorForStatus($table->status) }}"></span>
                                @else
                                    <input type="text" class="form-control" value="{{ ucfirst($table->status) }}"
                                        maxlength="30" disabled />
                                @endif
                            </div>
                        </div>
                    </td>
                @endforeach
                </tr>
            </table>
        </div>

        <div class="l-label" id="label">
            <i class="fas fa-toolbox" id="toolbar-toggle"></i>
        </div>
        <div class="l-delete" id="label2" ondrop="dropDelete(event)" ondragover="allowDrop(event)">
            <i class="fas fa-trash" id="toolbar-delete"></i>
        </div>
        <div class="l-toolbar" id="toolbar">
            <nav class="nav">
                <div class="d-flex flex-column text-center">
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 2)"
                            src="/img/tables/seat-2.png" title="2-seat table" /></div>
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 3)"
                            src="/img/tables/seat-3.png" title="3-seat table" /></div>
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 4)"
                            src="/img/tables/seat-4.png" title="4-seat table" /></div>
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 6)"
                            src="/img/tables/seat-6.png" title="6-seat table" /></div>
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 8)"
                            src="/img/tables/seat-8.png" title="8-seat table" /></div>
                    <div class="p-2"><img class="drag-img" draggable="true" ondragstart="drag(event, 10)"
                            src="/img/tables/seat-10.png" title="10-seat table" /></div>
                </div>
            </nav>
        </div>
    </div>

    <script>
        //--------------------------------------
        // Drag and drop
        //--------------------------------------
        var draggedItem;
        var deleteLabel = document.getElementById('label2');

        function drag(ev, data) {
            var td = $(ev.target).is('td') ? $(ev.target) : $(ev.target).closest('td');
            draggedItem = td;

            if (data != null) {
                // Drag from toolbar
                ev.dataTransfer.setData("type", 'image');
                ev.dataTransfer.setData("seat", data);
                ev.dataTransfer.setData("status", 'green');

            } else {
                // Show delete icon
                deleteLabel.style.display = "flex";

                // Drag from existing
                var statusDiv = td.find('div.status');

                if (statusDiv.find('span.dot').length > 0) {
                    // It is image
                    ev.dataTransfer.setData("type", 'image');

                    // Get seat no
                    var source = td.find('div.image img').attr('src');
                    var match = source.match(/(\d+)/);
                    var seat = match ? match[0] : 0;
                    ev.dataTransfer.setData("seat", seat);

                    // Get status color
                    var allClasses = statusDiv.find('span.dot').attr('class');
                    var classesArray = allClasses.split(' ');
                    var status = classesArray.filter(function(className) {
                        return className !== 'dot';
                    })[0];
                    ev.dataTransfer.setData("status", status);
                } else {
                    // It is text
                    ev.dataTransfer.setData("type", 'text');

                    // Get status
                    var status = statusDiv.find('input').val();
                    ev.dataTransfer.setData("status", status);
                }

            }
        }

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drop(ev) {
            ev.preventDefault();

            // Set target to td
            var target = $(ev.target).is('td') ? $(ev.target) : $(ev.target).closest('td');

            // Check if source and destination same
            if (draggedItem.get(0) === target.get(0)) {
                return;
            }

            // Define div
            var imageDiv = target.find('div.image');
            var statusDiv = target.find('div.status');

            // Get target status 
            var dotSpan = statusDiv.find('span.dot');
            if (dotSpan.length != 0) {
                var allClasses = dotSpan.attr('class');
                var classesArray = allClasses.split(' ');
                var targetStatus = classesArray.filter(function(className) {
                    return className !== 'dot';
                })[0];

                // Prevent table occupied/reserved (red/yellow) to be replaced
                if (targetStatus == 'red') {
                    sweetalert(
                        'Table with status <span style="color:#eb1515">Occupied</span> cannot be replaced. Please relocate it before attempting any changes.'
                    );
                    return;
                } else if (targetStatus == 'yellow') {
                    sweetalert(
                        'Table with status <span style="color:#dcb619">Reserved</span> cannot be replaced. Please relocate it before attempting any changes.'
                    );
                    return;
                }
            }

            // Get type
            var type = ev.dataTransfer.getData("type");

            if (type == 'image') {
                // Get seat and status
                var seat = ev.dataTransfer.getData("seat");
                var status = ev.dataTransfer.getData("status");

                var img = document.createElement("img");
                img.src = "/img/tables/seat-" + seat + ".png";
                img.className = "item-img";

                target.removeClass('item-empty');
                imageDiv.html(img);
                statusDiv.html('<span class="dot ' + status + '"></span>');

            } else if (type == 'text') {
                // Get status
                var status = ev.dataTransfer.getData("status");

                if (status == null || status == '') {
                    target.addClass('item-empty');
                } else {
                    target.removeClass('item-empty');
                }
                imageDiv.html('');
                statusDiv.html('<input type="text" class="form-control" value="' + status +
                    '" maxlength="30" disabled>');
            }

            draggedItem.addClass('item-empty');
            draggedItem.find('div.image').html('');
            draggedItem.find('div.status').html(
                '<input type="text" class="form-control" value="" maxlength="30" disabled>');

            target.click();

            dragEnd();
        }

        // Delete function
        deleteLabel.addEventListener('dragenter', function() {
            deleteLabel.classList.add('hovered');
        });


        deleteLabel.addEventListener('dragover', function() {
            deleteLabel.classList.add('hovered');
        });

        deleteLabel.addEventListener('dragleave', function() {
            deleteLabel.classList.remove('hovered');
        });

        function dragEnd() {
            // Hide delete icon
            deleteLabel.classList.remove('hovered');
            deleteLabel.style.display = "none";

            // Update table count
            setLatestCount();
        }

        function dropDelete(ev) {
            ev.preventDefault();

            var td = draggedItem;

            // Get target status 
            var dotSpan = td.find('div.status span.dot');
            if (dotSpan.length != 0) {
                var allClasses = dotSpan.attr('class');
                var classesArray = allClasses.split(' ');
                var targetStatus = classesArray.filter(function(className) {
                    return className !== 'dot';
                })[0];

                // Prevent table occupied/reserved (red/yellow) to be replaced
                if (targetStatus == 'red') {
                    sweetalert('Table with status <span style="color:#eb1515">Occupied</span> cannot be deleted.');
                    return;
                } else if (targetStatus == 'yellow') {
                    sweetalert('Table with status <span style="color:#dcb619">Reserved</span> cannot be deleted.');
                    return;
                }
            }

            td.addClass('item-empty');
            td.find('div.image').html('');
            td.find('div.status').html('<input type="text" class="form-control" value="" maxlength="30" disabled>');
            td.click();

            dragEnd();
        }

        function sweetalert(content) {
            Swal.fire({
                icon: 'warning',
                title: 'Caution',
                html: content,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
            })
        }

        function setLatestCount() {
            var tableCount = $('td:has(span.dot)').length;
            $('#table-count').text(tableCount + ' tables');
        }

        $(document).ready(function() {
            //--------------------------------------
            // Save data
            //--------------------------------------
            var originalData = getTableData();

            $('button#save').click(function() {
                var tableData = getTableData();

                var hasChanged = JSON.stringify(originalData) !== JSON.stringify(tableData);

                if (hasChanged) {
                    $.ajax({
                        type: 'POST',
                        url: '/tables',
                        data: {
                            data: tableData,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            // Handle response
                            Swal.fire({
                                icon: 'success',
                                title: 'Successful',
                                text: 'Table updated successfully!',
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                confirmButtonText: 'View',
                                confirmButtonColor: '#3085d6',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/tables';
                                }
                            })
                        },
                        error: function(error) {
                            // Handle errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Successful',
                                text: 'Table updated successfully!',
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                confirmButtonText: 'View',
                                confirmButtonColor: '#3085d6',
                            })
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes Made',
                        text: 'Table remain unchanged.',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        confirmButtonText: 'View',
                        confirmButtonColor: '#3085d6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/tables';
                        }
                    })
                }
            });

            function getTableData() {
                var tableData = [];

                $('td').each(function() {
                    // Get seat
                    var source = $(this).find('img.item-img')?.attr('src');
                    var match = source?.match(/(\d+)/);
                    var seat = match ? match[0] : 0;

                    // Get color
                    var allClasses = $(this).find('span.dot')?.attr('class');
                    var classesArray = allClasses?.split(' ');
                    var color = classesArray?.filter(function(className) {
                        return className !== 'dot';
                    })[0];

                    // Get status
                    var status;
                    switch (color) {
                        case 'red':
                            status = 'occupied';
                            break;
                        case 'green':
                            status = 'available';
                            break;
                        case 'yellow':
                            status = 'reserved';
                            break;
                        case 'gray':
                            status = 'out of service';
                            break;
                        default:
                            status = $(this).find('input').val();
                            // Not more than 30 characters
                            status = status?.slice(0, 30);
                            // First letter capital, others small
                            status = status?.charAt(0).toUpperCase() + status?.slice(1)
                                .toLowerCase();
                    }

                    var data = {
                        row: $(this).parent().index() + 1,
                        col: $(this).index() + 1,
                        seat: seat,
                        status: status,
                    };

                    tableData.push(data);
                });

                return tableData;
            }


            //--------------------------------------
            // Toolbar
            //--------------------------------------
            const showToolbar = (toggleId, navId, bodyId, labelId, label2Id) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    label = document.getElementById(labelId),
                    label2 = document.getElementById(label2Id)

                // validate that all variables exist
                if (toggle && nav && bodypd && label && label2) {
                    toggle.addEventListener('click', () => {
                        // show navbar
                        nav.classList.toggle('showToolbar')
                        // change icon
                        toggle.classList.toggle('fa-times')
                        // add padding to body
                        bodypd.classList.toggle('content-pd')
                        // move label
                        label.classList.toggle('moveLabel')
                        // move lavel 2
                        label2.classList.toggle('moveLabel')
                    })
                }
            }

            showToolbar('toolbar-toggle', 'toolbar', 'content-pd', 'label', 'label2');


            //--------------------------------------
            // Status change
            //--------------------------------------
            $('table').on('click', 'span.dot', function() {
                var allClasses = $(this).attr('class');
                var classesArray = allClasses.split(' ');
                var color = classesArray.filter(function(className) {
                    return className !== 'dot';
                })[0];

                var status;
                var change;

                if (color === 'green') {
                    status = 'Available';
                    change = 'Out of service';
                } else if (color === 'gray') {
                    status = 'Out of service';
                    change = 'Available';
                } else {
                    return;
                }

                var styles = {
                    'Available': 'color:#23d30c',
                    'Out of service': 'color:#9c9c9c'
                };

                var from = `<span style="${styles[status]}">${status}</span>`;
                var to = `<span style="${styles[change]}">${change}</span>`;

                var newColor = change === 'Available' ? 'green' : 'gray';

                Swal.fire({
                    icon: 'info',
                    title: 'Change Status',
                    html: 'Are you sure to change table status from ' + from + ' to ' + to + '?',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var td = $(this).closest('td');
                        $(this).replaceWith(`<span class="dot ${newColor}"></span>`);
                        td.click();
                    }
                })
            });


            //--------------------------------------
            // Input check
            //--------------------------------------
            $(document).on('input', 'input', function() {
                var input = $(this).val().trim();

                // Check if the trimmed value is not empty
                if (input !== '') {
                    // Trim the input to a maximum of 30 characters
                    input = input.slice(0, 30);
                    $(this).val(input);
                    $(this).closest('td').removeClass('item-empty');
                } else {
                    $(this).closest('td').addClass('item-empty');
                }
            });


            //--------------------------------------
            // Display of table
            //--------------------------------------
            $('#table-count').text('{{ $tableCounter }} tables');

            // Handle table item click
            $('table').on('click', 'td', function(e) {
                var borderNow = $(this).css('border-color');
                var borderColor = $(this).find('span').length ? $(this).find('span').css('border-color') :
                    'rgb(31, 87, 156)';

                if (borderNow == borderColor) {
                    if (!$(e.target).is('input')) {
                        deselect(this);
                    }
                } else {
                    select(this, borderColor);
                }

                // Enable plus top, bottom, left, right buttons
                addItem(this);

                // Enable minus top, bottom, left, right buttons
                removeItem(this);

                e.stopPropagation();
            });

            // Table item clicked
            function select(item, borderColor) {
                // Show border
                $('td').css('border-color', 'transparent');
                $(item).css('border-color', borderColor);

                // Show plus button
                $('div.add').removeClass('visible');
                $(item).find('div.add').addClass('visible');

                // Show minus button
                $('div.minus').removeClass('visible');
                $(item).find('div.minus').addClass('visible');

                // Enable input
                var inputElement = $(item).find('input');
                if (inputElement.prop('disabled')) {
                    $('td input').prop('disabled', true);
                    inputElement.prop('disabled', false);
                }

                // Show placeholder
                $('td input').attr('placeholder', '');
                inputElement.attr('placeholder', 'Name');
            }

            // Table item clicked again
            function deselect(item) {
                // Hide border
                $(item).css('border-color', 'transparent');

                // Hide plus button
                $('div.add').removeClass('visible');

                // Hide minus button
                $('div.minus').removeClass('visible');

                // Disable input
                $('td input').prop('disabled', true);

                // Hide placeholder
                $('td input').attr('placeholder', '');
            }

            // Generate empty td
            function getEmptyTd() {
                // <td class="text-center align-middle
                var newTd = document.createElement('td');
                newTd.classList.add('text-center', 'align-middle', 'item-empty');

                // ondrop="drop(event)" ondragover="allowDrop(event)"
                newTd.setAttribute('ondrop', 'drop(event)');
                newTd.setAttribute('ondragover', 'allowDrop(event)');

                // draggable="true" ondragstart="drag(event)" ondragend="dragEnd()">
                newTd.setAttribute('draggable', 'true');
                newTd.setAttribute('ondragstart', 'drag(event)');
                newTd.setAttribute('ondragend', 'dragEnd()');

                ['top', 'bottom', 'left', 'right'].forEach(function(direction) {
                    // <div class="add top"><i class="fas fa-plus-circle"></i></div>
                    // <div class="add bottom"><i class="fas fa-plus-circle"></i></div>
                    // <div class="add left"><i class="fas fa-plus-circle"></i></div>
                    // <div class="add right"><i class="fas fa-plus-circle"></i></div>
                    var addDiv = document.createElement('div');
                    addDiv.classList.add('add', direction);
                    addDiv.innerHTML = '<i class="fas fa-plus-circle"></i>';
                    newTd.appendChild(addDiv);

                    // <div class="minus top"><i class="fas fa-minus-circle"></i></div>
                    // <div class="minus bottom"><i class="fas fa-minus-circle"></i></div>
                    // <div class="minus left"><i class="fas fa-minus-circle"></i></div>
                    // <div class="minus right"><i class="fas fa-minus-circle"></i></div>
                    var minusDiv = document.createElement('div');
                    minusDiv.classList.add('minus', direction);
                    minusDiv.innerHTML = '<i class="fas fa-minus-circle"></i>';
                    newTd.appendChild(minusDiv);
                });

                // <div class="d-flex flex-column">
                var outerDiv = document.createElement('div');
                outerDiv.classList.add('d-flex', 'flex-column');

                // <div class="image">
                var imageDiv = document.createElement('div');
                imageDiv.classList.add('image');

                // <div class="status">
                var statusDiv = document.createElement('div');
                statusDiv.classList.add('status');

                // <input type="text" class="form-control" value="" maxlength="30" disabled />
                var inputElement = document.createElement('input');
                inputElement.setAttribute('type', 'text');
                inputElement.setAttribute('class', 'form-control');
                inputElement.setAttribute('value', '');
                inputElement.setAttribute('maxlength', '30');
                inputElement.setAttribute('disabled', '');

                statusDiv.appendChild(inputElement);
                outerDiv.appendChild(imageDiv);
                outerDiv.appendChild(statusDiv);
                newTd.appendChild(outerDiv);

                return newTd;
            }

            // Define once
            const emptyTd = $(getEmptyTd());
            const inputWidth = $('td').width();
            const maxRow = maxCol = 10;

            // Ensure empty item width
            $('td.item-empty div.status').css('width', inputWidth);

            // Handle add rows and cols
            function addItem(item) {
                var item = $(item);

                // Unbind the previous click event
                $('div.add.top').off('click');
                $('div.add.bottom').off('click');
                $('div.add.left').off('click');
                $('div.add.right').off('click');

                if ($('tr').length >= maxRow) {
                    $('div.add.top, div.add.bottom').hide();
                } else if ($('tr:first').find('td').length >= maxCol) {
                    $('div.add.left, div.add.right').hide();
                }

                // Top button
                $('div.add.top').click(function() {
                    var tr = item.parents('tr');
                    var newRow = $('<tr></tr>');

                    tr.find('td').each(function() {
                        var clonedTd = emptyTd.clone().removeAttr('id');
                        newRow.append(clonedTd);
                    });

                    tr.before(newRow);

                    if ($('tr').length >= maxRow) {
                        $('div.add.top, div.add.bottom').hide();
                    }
                });

                // Bottom button
                $('div.add.bottom').click(function() {
                    var tr = item.parents('tr');
                    var newRow = $('<tr></tr>');

                    tr.find('td').each(function() {
                        var clonedTd = emptyTd.clone().removeAttr('id');
                        newRow.append(clonedTd);
                    });

                    tr.after(newRow);

                    if ($('tr').length >= maxRow) {
                        $('div.add.top, div.add.bottom').hide();
                    }
                });

                // Left button
                $('div.add.left').click(function() {
                    var index = item.index();

                    $('tr').each(function() {
                        var clonedTd = emptyTd.clone().removeAttr('id');
                        clonedTd.find('div.status').width(inputWidth);
                        $(this).find('td').eq(index).before(clonedTd);
                    });

                    if ($('tr:first').find('td').length >= maxCol) {
                        $('div.add.left, div.add.right').hide();
                    }
                });

                // Right button
                $('div.add.right').click(function() {
                    var index = item.index();

                    $('tr').each(function() {
                        var clonedTd = emptyTd.clone().removeAttr('id');
                        clonedTd.find('div.status').width(inputWidth);
                        $(this).find('td').eq(index).after(clonedTd);
                    });

                    if ($('tr:first').find('td').length >= maxCol) {
                        $('div.add.left, div.add.right').hide();
                    }
                });
            }

            // Handle remove rows and cols
            function removeItem(item) {
                var item = $(item);

                // Unbind the previous click event
                $('div.minus.top').off('click');
                $('div.minus.bottom').off('click');
                $('div.minus.left').off('click');
                $('div.minus.right').off('click');

                $('div.minus').removeClass('disabled');
                if (item.prev('td').length === 0) {
                    $('div.minus.left').addClass('disabled');
                }
                if (item.next('td').length === 0) {
                    $('div.minus.right').addClass('disabled');
                }
                if (item.closest('tr').is(':first-child')) {
                    $('div.minus.top').addClass('disabled');
                }
                if (item.closest('tr').is(':last-child')) {
                    $('div.minus.bottom').addClass('disabled');
                }

                // Top button
                $('div.minus.top').click(function() {
                    if ($(this).hasClass('disabled')) {
                        return;
                    }

                    dialog('row above').then((result) => {
                        if (result) {
                            var tr = item.closest('tr');
                            var rowAbove = tr.prev('tr');

                            if (rowAbove.length > 0) {
                                rowAbove.remove();
                            }
                        }
                    });
                });

                // Bottom button
                $('div.minus.bottom').click(function() {
                    if ($(this).hasClass('disabled')) {
                        return;
                    }

                    dialog('row below').then((result) => {
                        if (result) {
                            var tr = item.closest('tr');
                            var rowAbove = tr.next('tr');

                            if (rowAbove.length > 0) {
                                rowAbove.remove();
                            }
                        }
                    });
                });

                // Left button
                $('div.minus.left').click(function() {
                    if ($(this).hasClass('disabled')) {
                        return;
                    }

                    dialog('column to the left').then((result) => {
                        if (result) {
                            var index = item.index() - 1;

                            $('tr').each(function() {
                                $(this).find('td:eq(' + index + ')').remove();
                            });
                        }
                    });
                });

                // Right button
                $('div.minus.right').click(function() {
                    if ($(this).hasClass('disabled')) {
                        return;
                    }

                    dialog('column to the right').then((result) => {
                        if (result) {
                            var index = item.index() + 1;

                            $('tr').each(function() {
                                $(this).find('td:eq(' + index + ')').remove();
                            });
                        }
                    });
                });
            }

            function dialog(text) {
                return new Promise((resolve) => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Caution',
                        html: 'Are you sure to delete <strong>the ' + text + '</strong>?',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonText: 'Yes',
                        confirmButtonColor: '#3085d6',
                    }).then((result) => {
                        resolve(result.isConfirmed);
                    });
                });
            }
        });
    </script>
@endsection
