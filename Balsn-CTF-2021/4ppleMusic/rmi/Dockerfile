FROM amd64/debian
COPY flag /my_secret_flag_is_here
COPY asm_linux_x64_v6_50_18579/manager/StorMan-6.50.x86_64.rpm /tmp/
RUN apt update
RUN apt-get install alien -y
RUN alien -i /tmp/StorMan-6.50.x86_64.rpm
COPY jre /usr/StorMan/jre
WORKDIR /usr/StorMan/
RUN chmod -R 755 jre/

ENTRYPOINT ["su", "nobody", "-s", "/bin/sh", "-c", "/usr/StorMan/jre/bin/java -Djava.compiler=NONE -cp /usr/StorMan/RaidMan.jar com.ibm.sysmgt.raidmgr.agent.ManagementAgent"]
