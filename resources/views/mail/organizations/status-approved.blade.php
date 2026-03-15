<x-mail::message>
# Your Organization Has Been Approved

Your organization **{{ $organization->title }}** has been approved.

You can now start using all features available to your organization.

<x-mail::button :url="route('dashboard.organizations.index')">
Go to Dashboard
</x-mail::button>

Thank you for joining us!
</x-mail::message>
