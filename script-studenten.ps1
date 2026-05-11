$studenten = @("stijn", "freark", "gerwin")

foreach ($naam in $studenten) {
    git checkout -b $naam
    git push origin $naam
    git checkout main
}