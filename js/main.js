document.addEventListener("DOMContentLoaded", () => {
  const loginBtn = document.getElementById("login-btn");
  const leagueData = document.getElementById("league-data");
  const standings = document.getElementById("standings");

  loginBtn.addEventListener("click", () => {
    fetch("api/auth.php")
      .then((response) => response.json())
      .then((data) => {
        window.location.href = data.authUrl;
      })
      .catch((error) => console.error("Error:", error));
  });

  function fetchLeagueData() {
    fetch("api/auth.php?action=getLeagueData")
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          return;
        }

        loginBtn.parentElement.classList.add("hidden");
        leagueData.classList.remove("hidden");

        displayLeagueData(data);
      })
      .catch((error) => console.error("Error:", error));
  }

  function displayLeagueData(data) {
    standings.innerHTML = "";

    data.fantasy_content.users[0].user[1].games.forEach((game) => {
      game.leagues.forEach((league) => {
        const leagueCard = document.createElement("div");
        leagueCard.className = "league-card";

        leagueCard.innerHTML = `
                    <h3>${league.name}</h3>
                    <p>League ID: ${league.league_id}</p>
                    <div class="standings-table">
                        ${createStandingsTable(league.standings)}
                    </div>
                `;

        standings.appendChild(leagueCard);
      });
    });
  }

  function createStandingsTable(standings) {
    return `
            <table>
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>W</th>
                        <th>L</th>
                        <th>T</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    ${standings.teams
                      .map(
                        (team) => `
                        <tr>
                            <td>${team.name}</td>
                            <td>${team.standings.wins}</td>
                            <td>${team.standings.losses}</td>
                            <td>${team.standings.ties}</td>
                            <td>${team.standings.points_for}</td>
                        </tr>
                    `
                      )
                      .join("")}
                </tbody>
            </table>
        `;
  }

  // Check if user is already authenticated
  fetchLeagueData();
});
