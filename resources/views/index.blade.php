<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center">
                <h1 class="text-lg text-white">Leaderboard</h1>

                <table class="text-white">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Luke', 'Raj', 'Sam'] as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user }}</td>
                                <td>{{ 22 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
