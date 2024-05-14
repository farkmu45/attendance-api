<x-layouts.app>
  <div class="mx-auto max-w-screen-lg rounded-lg bg-white p-6">
    <h2 class="mb-10 mt-14 text-2xl font-bold">Attendance Report</h2>

    <div class="mb-6">
      <div class="flex items-center">
        <img class="mr-4 h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->picture) }}"
          alt="User Picture">
        <div>
          <h3 class="text-lg font-bold">{{ $user->name }}</h3>
          <p class="text-gray-500">{{ $user->username }}</p>
        </div>
      </div>
    </div>

    <table class="w-full border-collapse">
      <thead>
        <tr>
          <th class="border-b px-4 py-2">Type</th>
          <th class="border-b px-4 py-2">Date</th>
          <th class="border-b px-4 py-2">Time</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($attendances as $attendance)
          <tr>
            <td class="border-b px-4 py-2">
              @if ($attendance->type === 'IN')
                Entrance
              @else
                Exit
              @endif
            </td>
            <td class="border-b px-4 py-2 text-center">{{ $attendance->time->format('Y-m-d') }}</td>
            <td class="border-b px-4 py-2 text-center">{{ $attendance->time->format('H:i') }}</td>
          </tr>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</x-layouts.app>
