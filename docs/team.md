---
layout: page

title: The Team
---

<script setup>
  import { VPTeamPage, VPTeamPageTitle, VPTeamPageSection, VPTeamMembers } from "vitepress/theme";
  import { teamMembers } from "./_data/team";
</script>

<VPTeamPage>
  <VPTeamPageTitle>
    <template #title>The Team</template>
    <template #lead>
      The development of Phyre Panel is guided by an international team, some of whom have chosen to be featured below.
    </template>
  </VPTeamPageTitle>
  <VPTeamPageSection>
    <template #title>Team Members</template>
    <template #members>
      <VPTeamMembers :members="teamMembers" />
    </template>
  </VPTeamPageSection>
  <!-- <VPTeamPageSection>
    <template #title>Contributors ❤️</template>
    <template #members>
      <VPTeamMembers size="small" :members="featuredContributors" />
    </template>
  </VPTeamPageSection> -->
</VPTeamPage>
