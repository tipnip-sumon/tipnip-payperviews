<x-smart_layout>
    @section('top_title','Profile')
    @section('title','Profile')
    @section('content')
    <div class="row">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-title">Basic info:</div>
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" placeholder="First Name">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Email address</label>
                            <input type="email" class="form-control" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="number" class="form-control" placeholder="Number">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" placeholder="Home Address">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" placeholder="City">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="number" class="form-control" placeholder="ZIP Code">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <select data-placeholder="Select Country" class="form-control Country" data-trigger>
                                <option value="">Select Country</option>
                                <option value="1">Germany</option>
                                <option value="2">Canada</option>
                                <option value="3">Usa</option>
                                <option value="4">Afghanistan</option>
                                <option value="5">Albania</option>
                                <option value="6">China</option>
                                <option value="7">Denmark</option>
                                <option value="8">Finland</option>
                                <option value="9">India</option>
                                <option value="10">Kiribati</option>
                                <option value="11">Kuwait</option>
                                <option value="12">Mexico</option>
                                <option value="13">Pakistan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-title mt-4">External Links:</div>
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Facebook</label>
                            <input type="text" class="form-control" placeholder="https://www.facebook.com/">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Google</label>
                            <input type="text" class="form-control" placeholder="https://www.google.com/">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Twitter</label>
                            <input type="text" class="form-control" placeholder="https://twitter.com/">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Pinterest</label>
                            <input type="text" class="form-control" placeholder="https://in.pinterest.com/">
                        </div>
                    </div>
                </div>
                <div class="card-title mt-4">About:</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">About Me</label>
                            <textarea rows="5" class="form-control" placeholder="Enter About your description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a  href="javascript:void(0);" class="btn btn-lg btn-primary">Updated</a>
                <a  href="javascript:void(0);" class="btn btn-lg btn-danger">Cancel</a>
            </div>
        </div>
    </div>
    @endsection
</x-smart_layout>