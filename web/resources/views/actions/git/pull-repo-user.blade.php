echo "Pull started at {{ date('Y-m-d H:i:s') }}"

cd {{$projectDir}}

git clean -f -d
git reset --hard HEAD~2

@if($privateKeyFile)

git -c core.sshCommand="ssh -i {{$privateKeyFile}}" pull

@else

git pull --rebase

@endif

@if($deploymentScript)
{!! $deploymentScript !!}
@endif

{{--rm -rf {{$selfFile}}--}}
