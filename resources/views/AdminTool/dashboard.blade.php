@extends('templates.main')
@section('content')
<div class="cardBox">
    <div class="row">
        <div class="col-12 col-lg-3">
            <div class="card">
                <div>
                    <div class="numbers">100</div>
                    <div class="cardName">Agencies</div>
                </div>
                <div class="iconBox">
                    <i class="fas fa-laptop-code"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div>
                    <div class="numbers">120</div>
                    <div class="cardName">Clinets</div>
                </div>
                <div class="iconBox">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div>
                    <div class="numbers">50</div>
                    <div class="cardName">Waiting</div>
                </div>
                <div class="iconBox">
                    <i class="far fa-address-book"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div>
                    <div class="numbers">30</div>
                    <div class="cardName">Finished Projects</div>
                </div>
                <div class="iconBox">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="details">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="recentAgencies">
                <div class="cardHeader">
                    <h2>Recent Agencies</h2>
                    <a href="#" class="view-all-btn">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Country</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>agency 1</td>
                            <td>agency1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>agency 1</td>
                            <td>agency1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>agency 1</td>
                            <td>agency1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>agency 1</td>
                            <td>agency1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="recentAgencies">
                <div class="cardHeader">
                    <h2>Recent Clinets</h2>
                    <a href="#" class="view-all-btn">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Country</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>
                        <tr>
                            <td>clinet 1</td>
                            <td>clinet1@test.com</td>
                            <td>Jordan</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection