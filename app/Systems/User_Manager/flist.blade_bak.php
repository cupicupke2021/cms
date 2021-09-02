@php 
$aryData    = $aryCont['aryData'];
$ctrlGrid   = $aryCont['ctrlGrid'];


@endphp 
<table id="example" class="display nowrap table" style="width:100%" >
        <thead>
            <tr>
                <th>No</th>
                <th>Userid</th>
                <th>Username</th>
                <th>Password</th>
                <th>Company</th>
                <th>Email</th>
                <th>Posisiton Code</th>
                <th>Image</th>
                @php 
                echo $ctrlGrid['edit']['header'];
                echo $ctrlGrid['delete']['header'];
                @endphp 
            </tr>
        </thead>
        <tbody>
        @php
        $no = 1;

        @endphp
        @foreach($aryData as $data)
        <tr>
        <td>{{ $no }}</td>
        <td>{{ $data['userid'] }}</td>
        <td>{{ $data['username'] }}</td>
        <td>{{ $data['password'] }}</td>
        <td>{{ $data['company'] }}</td>
        <td>{{ $data['email'] }}</td>
        <td>{{ $data['posisiton_code'] }}</td>
        <td>{{ $data['image'] }}</td>
        @php 
        echo $ctrlGrid['edit']['contrl'];
        echo $ctrlGrid['delete']['contrl'];
        @endphp 
        </tr>
        @php
        $no++;
        @endphp
        @endforeach

       
        </tbody>
</table>
<script>
$(document).ready(function() {
    var table = $('#example').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
} );
</script>