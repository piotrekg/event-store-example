FROM sebp/elk

WORKDIR ${LOGSTASH_HOME}
RUN gosu logstash bin/logstash-plugin install logstash-input-gelf
ADD 00-gelf-input.conf /etc/logstash/conf.d/