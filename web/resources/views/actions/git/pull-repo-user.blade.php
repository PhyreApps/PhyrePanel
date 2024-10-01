echo "Pull started at {{ date('Y-m-d H:i:s') }}"

cd {{$projectDir}}

git reset --hard

@if($privateKeyFile)

git -c core.sshCommand="ssh -i {{$privateKeyFile}}" pull

@else

git pull

@endif

rm -rf {{$selfFile}}
