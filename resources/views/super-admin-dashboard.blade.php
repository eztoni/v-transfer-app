<x-app-layout>

<div class="card bg-base-100 shadow-lg">

    <div class="card-body ">
        <h2 class="card-title">
            User Overview
        </h2>
        <table class="ds-table  w-full">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)


                <tr>
                    <th>{{ $user->id }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->getRoleNames() }}</td>

                    @if($user->hasRole('super-admin'))
                        <td>
                            <button disabled class="btn btn-primary btn-sm disabled">Edit</button>
                        </td>
                    @else
                        <td>
                                <a href="{{ route('edit-user',$user->id) }}"><button class="btn btn-sm btn-primary">Edit</button></a>
                        </td>
                    @endif

                </tr>

            @empty
                <tr>
                    <td colspan="999">
                        <div class="alert alert-warning">
                            <div class="flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     class="w-6 h-6 mx-2 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <label>No Users Found</label>
                            </div>
                        </div>
                    </TD>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>
</div>
</x-app-layout>

